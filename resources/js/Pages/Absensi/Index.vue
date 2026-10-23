<script setup>
import {
    ref,
    shallowRef,
    computed,
    onMounted,
    onBeforeUnmount,
    nextTick,
} from 'vue'

import { Head } from '@inertiajs/vue3'
import axios from 'axios'
import PesertaLayout from '@/Layouts/PesertaLayout.vue'

defineOptions({
    layout: PesertaLayout,
})

const props = defineProps({
    jadwal: {
        type: Object,
        default: null,
    },

    jadwals: {
        type: Array,
        default: () => [],
    },

    recentAttendances: {
        type: Array,
        default: () => [],
    },
})

/*
|--------------------------------------------------------------------------
| CAMERA
|--------------------------------------------------------------------------
*/

const video = ref(null)
const canvas = ref(null)
const stream = ref(null)

const cameraReady = ref(false)
const cameraError = ref('')

/*
|--------------------------------------------------------------------------
| MEDIAPIPE
|--------------------------------------------------------------------------
*/

const faceLandmarker = shallowRef(null)

const mediaPipeReady = ref(false)
const mediaPipeError = ref('')

let animationFrame = null
let lastVideoTime = -1

/*
|--------------------------------------------------------------------------
| LIVENESS — CORE
|--------------------------------------------------------------------------
*/

const challenges = [
    {
        key: 'blink',
        label: 'KEDIP',
        instruction: 'Kedipkan mata Anda',
    },
    {
        key: 'left',
        label: 'TOLEH KIRI',
        instruction: 'Tolehkan kepala ke KIRI',
    },
    {
        key: 'right',
        label: 'TOLEH KANAN',
        instruction: 'Tolehkan kepala ke KANAN',
    },
    {
        key: 'up',
        label: 'LIHAT ATAS',
        instruction: 'Lihat ke ATAS',
    },
    {
        key: 'down',
        label: 'LIHAT BAWAH',
        instruction: 'Lihat ke BAWAH',
    },
]

/*
|--------------------------------------------------------------------------
| LIVENESS — QUEUE CHALLENGE (3 berurutan)
|--------------------------------------------------------------------------
*/

const activeChallenges = ref([])
const currentChallengeIndex = ref(0)

const currentChallenge = computed(() => {
    return activeChallenges.value[currentChallengeIndex.value] ?? null
})

const challengePassed = ref(false)
const challengeRunning = ref(false)

const livenessStartedAt = ref(null)

const livenessTimeout = 15000 // timeout per challenge
const LIVENESS_TTL = 30000 // valid 30 detik setelah selesai

const livenessMessage = ref('Menyiapkan challenge...')

/*
|--------------------------------------------------------------------------
| LIVENESS — FRAME STATE
|--------------------------------------------------------------------------
*/

const blinkState = ref('open')
const blinkClosedFrames = ref(0)
const blinkOpenFrames = ref(0)

const headLeftFrames = ref(0)
const headRightFrames = ref(0)
const headUpFrames = ref(0)
const headDownFrames = ref(0)

const blinkDetected = ref(false)
const headLeftDetected = ref(false)
const headRightDetected = ref(false)
const headUpDetected = ref(false)
const headDownDetected = ref(false)

/*
|--------------------------------------------------------------------------
| LIVENESS — THRESHOLD
|--------------------------------------------------------------------------
*/

const BLINK_CLOSE_THRESHOLD = 0.17
const BLINK_OPEN_THRESHOLD = 0.28

const HEAD_YAW_THRESHOLD = 0.10
const HEAD_PITCH_THRESHOLD = 0.06

const REQUIRED_FRAMES = 3
const HOLD_FRAMES = 5

const MIN_BLINK_DURATION = 80
const MAX_BLINK_DURATION = 800

let blinkStartTime = null

/*
|--------------------------------------------------------------------------
| LIVENESS — HEAD POSE
|--------------------------------------------------------------------------
*/

const yaw = ref(0)
const pitch = ref(0)
const roll = ref(0)

/*
|--------------------------------------------------------------------------
| LIVENESS — MICRO MOVEMENT
|--------------------------------------------------------------------------
*/

const noseHistory = ref([])
const MAX_NOSE_HISTORY = 30
const MIN_MOVEMENT_VARIANCE = 0.00002

/*
|--------------------------------------------------------------------------
| LIVENESS — FACE SIGNATURE
|--------------------------------------------------------------------------
*/

let faceSignatureBaseline = null

/*
|--------------------------------------------------------------------------
| RECOGNITION
|--------------------------------------------------------------------------
*/

const recognizing = ref(false)

const recognitionMessage = ref('')
const recognitionError = ref('')

const successData = ref(null)

/*
|--------------------------------------------------------------------------
| 5 ABSENSI TERBARU
|--------------------------------------------------------------------------
*/

const recentAttendances = ref([
    ...props.recentAttendances,
])

/*
|--------------------------------------------------------------------------
| JADWAL
|--------------------------------------------------------------------------
*/

const selectedJadwalId = ref(props.jadwal?.id ?? '')

const selectedJadwal = computed(() => {
    if (props.jadwal) {
        return props.jadwal
    }

    return (
        props.jadwals.find(
            (item) =>
                Number(item.id) ===
                Number(selectedJadwalId.value)
        ) ?? null
    )
})

/*
|--------------------------------------------------------------------------
| FORMAT
|--------------------------------------------------------------------------
*/

const formatTanggal = (tanggal) => {
    if (!tanggal) return '-'

    const date = new Date(tanggal)
    if (isNaN(date.getTime())) return tanggal

    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    })
}

const formatJam = (jam) => {
    if (!jam) return '-'
    return String(jam).substring(0, 5)
}

const formatTime = (value) => {
    if (!value) return '-'

    const date = new Date(value)
    if (isNaN(date.getTime())) {
        return String(value).substring(0, 5)
    }

    return date.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
    })
}

/*
|--------------------------------------------------------------------------
| INFO JADWAL
|--------------------------------------------------------------------------
*/

const namaProgram = computed(() => {
    return (
        selectedJadwal.value?.program_pelatihan
            ?.pelatihan?.nama ?? '-'
    )
})

const namaTahap = computed(() => {
    return (
        selectedJadwal.value?.program_pelatihan
            ?.tahap?.nama ?? '-'
    )
})

const namaKegiatan = computed(() => {
    return selectedJadwal.value?.kegiatan ?? '-'
})

/*
|--------------------------------------------------------------------------
| MEDIAPIPE INITIALIZATION
|--------------------------------------------------------------------------
*/

const initFaceLandmarker = async () => {
    try {
        mediaPipeError.value = ''

        const { FaceLandmarker, FilesetResolver } = await import(
            '@mediapipe/tasks-vision'
        )

        const vision = await FilesetResolver.forVisionTasks(
            '/mediapipe/wasm'
        )

        faceLandmarker.value =
            await FaceLandmarker.createFromOptions(vision, {
                baseOptions: {
                    modelAssetPath:
                        '/mediapipe/models/face_landmarker.task',
                },
                runningMode: 'VIDEO',
                numFaces: 1,
                minFaceDetectionConfidence: 0.5,
                minFacePresenceConfidence: 0.5,
                minTrackingConfidence: 0.5,
                outputFaceBlendshapes: false,
            })

        mediaPipeReady.value = true

        console.log('MediaPipe Face Landmarker siap.')
    } catch (error) {
        console.error('Gagal initialize MediaPipe:', error)

        mediaPipeError.value =
            'MediaPipe gagal dimuat. Pastikan file WASM dan face_landmarker.task tersedia.'
    }
}

/*
|--------------------------------------------------------------------------
| CAMERA
|--------------------------------------------------------------------------
*/

const startCamera = async () => {
    try {
        cameraError.value = ''

        if (!navigator.mediaDevices?.getUserMedia) {
            throw new Error('Browser tidak mendukung kamera.')
        }

        stream.value =
            await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'user',
                    width: { ideal: 640 },
                    height: { ideal: 480 },
                },
                audio: false,
            })

        await nextTick()

        if (!video.value) {
            throw new Error('Element video tidak ditemukan.')
        }

        video.value.srcObject = stream.value
        await video.value.play()

        cameraReady.value = true

        startDetection()
    } catch (error) {
        console.error('Camera error:', error)

        cameraError.value =
            'Kamera tidak dapat digunakan. Pastikan izin kamera sudah diberikan.'
    }
}

const stopCamera = () => {
    if (animationFrame) {
        cancelAnimationFrame(animationFrame)
        animationFrame = null
    }

    if (stream.value) {
        stream.value.getTracks().forEach((track) => track.stop())
        stream.value = null
    }

    cameraReady.value = false
}

/*
|--------------------------------------------------------------------------
| RANDOM CHALLENGE QUEUE (3 berurutan dari 5)
|--------------------------------------------------------------------------
*/

const generateChallenges = () => {
    const shuffled = [...challenges].sort(() => Math.random() - 0.5)
    activeChallenges.value = shuffled.slice(0, 3)
    currentChallengeIndex.value = 0

    resetFrameState()

    faceSignatureBaseline = null
    noseHistory.value = []

    livenessStartedAt.value = Date.now()
    challengePassed.value = false
    challengeRunning.value = true

    livenessMessage.value = `Challenge 1/3: ${currentChallenge.value?.instruction}`

    console.log(
        '🎯 Challenge queue:',
        activeChallenges.value.map((c) => c.key)
    )
}

/*
|--------------------------------------------------------------------------
| RESET FRAME STATE
|--------------------------------------------------------------------------
*/

const resetFrameState = () => {
    blinkState.value = 'open'
    blinkClosedFrames.value = 0
    blinkOpenFrames.value = 0

    headLeftFrames.value = 0
    headRightFrames.value = 0
    headUpFrames.value = 0
    headDownFrames.value = 0

    blinkDetected.value = false
    headLeftDetected.value = false
    headRightDetected.value = false
    headUpDetected.value = false
    headDownDetected.value = false

    blinkStartTime = null
}

/*
|--------------------------------------------------------------------------
| START LIVENESS
|--------------------------------------------------------------------------
*/

const startLiveness = () => {
    if (!mediaPipeReady.value) {
        mediaPipeError.value = 'MediaPipe belum siap.'
        return
    }

    if (!cameraReady.value) {
        cameraError.value = 'Kamera belum siap.'
        return
    }

    successData.value = null
    recognitionError.value = ''
    recognitionMessage.value = ''

    generateChallenges()
}

/*
|--------------------------------------------------------------------------
| FACE LANDMARK HELPERS
|--------------------------------------------------------------------------
*/

const distance = (a, b) => {
    const dx = a.x - b.x
    const dy = a.y - b.y
    return Math.sqrt(dx * dx + dy * dy)
}

const calculateEAR = (landmarks, indices) => {
    const p1 = landmarks[indices[0]]
    const p2 = landmarks[indices[1]]
    const p3 = landmarks[indices[2]]
    const p4 = landmarks[indices[3]]
    const p5 = landmarks[indices[4]]
    const p6 = landmarks[indices[5]]

    if (!p1 || !p2 || !p3 || !p4 || !p5 || !p6) {
        return 0
    }

    const vertical1 = distance(p2, p6)
    const vertical2 = distance(p3, p5)
    const horizontal = distance(p1, p4)

    if (!horizontal) return 0

    return (vertical1 + vertical2) / (2 * horizontal)
}

/*
|--------------------------------------------------------------------------
| HEAD POSE (YAW / PITCH / ROLL)
|--------------------------------------------------------------------------
*/

const calculateHeadPose = (landmarks) => {
    const nose = landmarks[1]
    const leftEye = landmarks[33]
    const rightEye = landmarks[263]
    const chin = landmarks[152]
    const forehead = landmarks[10]
    const leftEar = landmarks[234]
    const rightEar = landmarks[454]

    if (
        !nose ||
        !leftEye ||
        !rightEye ||
        !chin ||
        !forehead ||
        !leftEar ||
        !rightEar
    ) {
        return { yaw: 0, pitch: 0, roll: 0 }
    }

    const distLeft = Math.abs(nose.x - leftEye.x)
    const distRight = Math.abs(nose.x - rightEye.x)
    const totalX = distLeft + distRight

    const yawValue = totalX > 0 ? (distRight - distLeft) / totalX : 0

    const distTop = Math.abs(nose.y - forehead.y)
    const distBottom = Math.abs(nose.y - chin.y)
    const totalY = distTop + distBottom

    const pitchValue =
        totalY > 0 ? (distBottom - distTop) / totalY : 0

    const rollValue = Math.atan2(
        rightEye.y - leftEye.y,
        rightEye.x - leftEye.x
    )

    return {
        yaw: yawValue,
        pitch: pitchValue,
        roll: rollValue,
    }
}

/*
|--------------------------------------------------------------------------
| MICRO MOVEMENT
|--------------------------------------------------------------------------
*/

const updateMicroMovement = (landmarks) => {
    const nose = landmarks[1]
    if (!nose) return

    noseHistory.value.push({ x: nose.x, y: nose.y })

    if (noseHistory.value.length > MAX_NOSE_HISTORY) {
        noseHistory.value.shift()
    }
}

const getMovementVariance = () => {
    if (noseHistory.value.length < MAX_NOSE_HISTORY) {
        return 1
    }

    const xs = noseHistory.value.map((p) => p.x)
    const ys = noseHistory.value.map((p) => p.y)

    const avgX = xs.reduce((a, b) => a + b, 0) / xs.length
    const avgY = ys.reduce((a, b) => a + b, 0) / ys.length

    const varX =
        xs.reduce((sum, x) => sum + (x - avgX) ** 2, 0) / xs.length
    const varY =
        ys.reduce((sum, y) => sum + (y - avgY) ** 2, 0) / ys.length

    return varX + varY
}

/*
|--------------------------------------------------------------------------
| FACE SIGNATURE (ANTI FACE SWAP)
|--------------------------------------------------------------------------
*/

const computeFaceSignature = (landmarks) => {
    const indices = [1, 33, 263, 61, 291, 152, 10, 234, 454]

    return indices
        .map((i) => {
            const p = landmarks[i]
            return p ? `${p.x.toFixed(3)},${p.y.toFixed(3)}` : '0,0'
        })
        .join('|')
}

const isFaceConsistent = (landmarks) => {
    const signature = computeFaceSignature(landmarks)

    if (!faceSignatureBaseline) {
        faceSignatureBaseline = signature
        return true
    }

    const a = faceSignatureBaseline
        .split('|')
        .map((v) => v.split(',').map(Number))
    const b = signature
        .split('|')
        .map((v) => v.split(',').map(Number))

    if (a.length !== b.length) return false

    let totalDist = 0

    for (let i = 0; i < a.length; i++) {
        const dx = a[i][0] - b[i][0]
        const dy = a[i][1] - b[i][1]
        totalDist += Math.sqrt(dx * dx + dy * dy)
    }

    const avgDist = totalDist / a.length

    return avgDist < 0.15
}

/*
|--------------------------------------------------------------------------
| BLINK DETECTION
|--------------------------------------------------------------------------
*/

const detectBlink = (landmarks) => {
    const leftEAR = calculateEAR(landmarks, [
        33, 160, 158, 133, 153, 144,
    ])

    const rightEAR = calculateEAR(landmarks, [
        362, 385, 387, 263, 373, 380,
    ])

    const ear = (leftEAR + rightEAR) / 2

    if (!ear || !Number.isFinite(ear)) return false

    // Mata tertutup
    if (ear < BLINK_CLOSE_THRESHOLD) {
        if (!blinkStartTime) {
            blinkStartTime = performance.now()
        }

        blinkClosedFrames.value++
        blinkOpenFrames.value = 0

        if (blinkClosedFrames.value >= REQUIRED_FRAMES) {
            blinkState.value = 'closed'
        }

        return false
    }

    // Mata terbuka kembali
    if (ear > BLINK_OPEN_THRESHOLD) {
        blinkOpenFrames.value++

        if (
            blinkState.value === 'closed' &&
            blinkOpenFrames.value >= REQUIRED_FRAMES
        ) {
            const duration = blinkStartTime
                ? performance.now() - blinkStartTime
                : 0

            // Tolak kedipan tidak natural
            if (
                duration < MIN_BLINK_DURATION ||
                duration > MAX_BLINK_DURATION
            ) {
                blinkState.value = 'open'
                blinkClosedFrames.value = 0
                blinkOpenFrames.value = 0
                blinkStartTime = null

                livenessMessage.value =
                    '⚠️ Kedipan tidak natural. Coba lagi.'
                return false
            }

            // Anti foto statis
            if (getMovementVariance() < MIN_MOVEMENT_VARIANCE) {
                livenessMessage.value =
                    '⚠️ Terdeteksi gambar statis.'
                return false
            }

            blinkDetected.value = true

            console.log('✅ KEDIP TERDETEKSI')

            return true
        }
    }

    return false
}

/*
|--------------------------------------------------------------------------
| HOLD CHALLENGE (untuk pose)
|--------------------------------------------------------------------------
*/

const holdFrames = ref(0)

const holdChallenge = (condition, hint) => {
    if (condition) {
        holdFrames.value++

        livenessMessage.value = `✓ Tahan... ${holdFrames.value}/${HOLD_FRAMES}`

        if (holdFrames.value >= HOLD_FRAMES) {
            return true
        }
    } else {
        holdFrames.value = 0

        livenessMessage.value = currentChallenge.value?.instruction
        if (hint) {
            livenessMessage.value += ` (${hint})`
        }
    }

    return false
}

/*
|--------------------------------------------------------------------------
| CHECK CHALLENGE
|--------------------------------------------------------------------------
*/

const checkChallenge = (landmarks, pose) => {
    if (!challengeRunning.value) return

    if (!currentChallenge.value) return

    // Timeout per challenge
    if (
        Date.now() - livenessStartedAt.value >
        livenessTimeout
    ) {
        challengeRunning.value = false

        recognitionError.value =
            'Liveness gagal. Waktu habis. Silakan coba lagi.'

        return
    }

    // Cek konsistensi wajah
    if (!isFaceConsistent(landmarks)) {
        generateChallenges()
        livenessMessage.value =
            '⚠️ Wajah berubah. Silakan ulangi dari awal.'
        return
    }

    updateMicroMovement(landmarks)

    yaw.value = pose.yaw
    pitch.value = pose.pitch
    roll.value = pose.roll

    let passed = false

    switch (currentChallenge.value.key) {
        case 'blink':
            passed = detectBlink(landmarks)
            break

        case 'left':
            passed = holdChallenge(
                pose.yaw > HEAD_YAW_THRESHOLD,
                'Tengok lebih ke KIRI'
            )
            break

        case 'right':
            passed = holdChallenge(
                pose.yaw < -HEAD_YAW_THRESHOLD,
                'Tengok lebih ke KANAN'
            )
            break

        case 'up':
            passed = holdChallenge(
                pose.pitch > HEAD_PITCH_THRESHOLD,
                'Lihat lebih ke ATAS'
            )
            break

        case 'down':
            passed = holdChallenge(
                pose.pitch < -HEAD_PITCH_THRESHOLD,
                'Lihat lebih ke BAWAH'
            )
            break
    }

    if (passed) {
        passChallenge()
    }
}

/*
|--------------------------------------------------------------------------
| PASS CHALLENGE
|--------------------------------------------------------------------------
*/

const passChallenge = async () => {
    if (!challengeRunning.value) return

    console.log(
        '✅ Challenge passed:',
        currentChallenge.value?.key
    )

    // Pindah ke challenge berikutnya
    currentChallengeIndex.value++
    resetFrameState()
    holdFrames.value = 0
    livenessStartedAt.value = Date.now()

    // Semua challenge selesai
    if (
        currentChallengeIndex.value >=
        activeChallenges.value.length
    ) {
        challengePassed.value = true
        challengeRunning.value = false

        livenessMessage.value =
            'Liveness berhasil. Mengambil foto...'

        await new Promise((resolve) =>
            setTimeout(resolve, 500)
        )

        await captureAndRecognize()

        return
    }

    livenessMessage.value = `Challenge ${
        currentChallengeIndex.value + 1
    }/${activeChallenges.value.length}: ${
        currentChallenge.value?.instruction
    }`

    console.log(
        '➡️ Next challenge:',
        currentChallenge.value?.key
    )
}

/*
|--------------------------------------------------------------------------
| FACE DETECTION LOOP
|--------------------------------------------------------------------------
*/

const startDetection = () => {
    if (!video.value) return

    const detect = () => {
        if (
            !faceLandmarker.value ||
            !video.value ||
            video.value.readyState < 2
        ) {
            animationFrame = requestAnimationFrame(detect)
            return
        }

        try {
            const currentTime = video.value.currentTime

            if (currentTime !== lastVideoTime) {
                lastVideoTime = currentTime

                const timestamp = Math.round(
                    performance.now()
                )

                const result =
                    faceLandmarker.value.detectForVideo(
                        video.value,
                        timestamp
                    )

                const allFaces =
                    result?.faceLandmarks ?? []

                // Multiple faces
                if (allFaces.length > 1) {
                    if (challengeRunning.value) {
                        livenessMessage.value =
                            '⚠️ Hanya boleh 1 wajah di frame.'
                        resetFrameState()
                    }
                } else if (allFaces.length === 1) {
                    const landmarks = allFaces[0]

                    if (
                        Array.isArray(landmarks) &&
                        landmarks.length > 0 &&
                        challengeRunning.value
                    ) {
                        const pose =
                            calculateHeadPose(landmarks)

                        checkChallenge(landmarks, pose)
                    }
                } else {
                    // Tidak ada wajah
                    if (challengeRunning.value) {
                        livenessMessage.value =
                            'Wajah tidak terdeteksi. Posisikan wajah di tengah.'
                        resetFrameState()
                    }
                }
            }
        } catch (error) {
            console.error('Face detection error:', error)
        }

        animationFrame = requestAnimationFrame(detect)
    }

    animationFrame = requestAnimationFrame(detect)
}

/*
|--------------------------------------------------------------------------
| CAPTURE PHOTO
|--------------------------------------------------------------------------
*/

const capturePhoto = () => {
    return new Promise((resolve, reject) => {
        if (!video.value || !canvas.value) {
            reject(new Error('Kamera belum siap.'))
            return
        }

        const width = video.value.videoWidth
        const height = video.value.videoHeight

        if (!width || !height) {
            reject(
                new Error('Ukuran kamera belum tersedia.')
            )
            return
        }

        canvas.value.width = width
        canvas.value.height = height

        const context = canvas.value.getContext('2d')

        context.drawImage(
            video.value,
            0,
            0,
            width,
            height
        )

        // Watermark
        context.fillStyle = 'rgba(255,255,255,0.85)'
        context.font = 'bold 18px monospace'
        context.fillText(
            `TIME: ${new Date().toISOString()}`,
            20,
            height - 50
        )
        context.fillText(
            `CHALLENGE: ${activeChallenges.value
                .map((c) => c.key)
                .join(',')}`,
            20,
            height - 20
        )

        canvas.value.toBlob(
            (blob) => {
                if (!blob) {
                    reject(
                        new Error('Gagal mengambil foto.')
                    )
                    return
                }

                resolve(blob)
            },
            'image/jpeg',
            0.9
        )
    })
}

/*
|--------------------------------------------------------------------------
| NORMALIZE RECENT ATTENDANCE
|--------------------------------------------------------------------------
*/

const normalizeAttendance = (item) => {
    if (
        item?.peserta ||
        item?.program_pelatihan ||
        item?.absensi
    ) {
        return item
    }

    const peserta = item?.pendaftaran?.peserta
    const kelas = item?.pendaftaran?.kelas
    const program = item?.jadwal?.program_pelatihan
    const absensi = item

    return {
        peserta: {
            id: peserta?.id,
            nama: peserta?.nama ?? '-',
        },
        program_pelatihan: {
            id: program?.id,
            nama: program?.nama ?? '-',
            pelatihan: program?.pelatihan?.nama ?? '-',
            tahap: program?.tahap?.nama ?? '-',
        },
        kelas: {
            id: kelas?.id,
            nama: kelas?.nama ?? '-',
        },
        jadwal: {
            id: item?.jadwal?.id,
            pertemuan: item?.jadwal?.pertemuan ?? '-',
        },
        absensi: {
            id: absensi?.id,
            waktu_masuk: absensi?.waktu_masuk,
            status: absensi?.status,
            metode: absensi?.metode,
        },
        face_score:
            item?.face_score ?? absensi?.face_score ?? null,
    }
}

/*
|--------------------------------------------------------------------------
| TAMBAH KE 5 ABSENSI TERBARU
|--------------------------------------------------------------------------
*/

const addRecentAttendance = (data) => {
    const normalized = normalizeAttendance(data)
    const id = normalized?.absensi?.id

    if (id) {
        recentAttendances.value =
            recentAttendances.value.filter(
                (item) =>
                    Number(
                        normalizeAttendance(item)?.absensi?.id
                    ) !== Number(id)
            )
    }

    recentAttendances.value.unshift(normalized)
    recentAttendances.value =
        recentAttendances.value.slice(0, 5)
}

/*
|--------------------------------------------------------------------------
| SEND TO LARAVEL
|--------------------------------------------------------------------------
*/

const captureAndRecognize = async () => {
    if (recognizing.value) return

    if (!selectedJadwalId.value) {
        recognitionError.value =
            'Silakan pilih jadwal terlebih dahulu.'
        return
    }

    recognizing.value = true
    recognitionMessage.value = 'Mengambil foto...'
    recognitionError.value = ''

    try {
        const blob = await capturePhoto()

        recognitionMessage.value = 'Mengenali wajah...'

        const formData = new FormData()
        formData.append('image', blob, 'attendance.jpg')
        formData.append(
            'jadwal_id',
            selectedJadwalId.value
        )

        // Data liveness untuk verifikasi server
        formData.append(
            'liveness_challenges',
            JSON.stringify(
                activeChallenges.value.map((c) => c.key)
            )
        )
        formData.append(
            'liveness_completed_at',
            String(Date.now())
        )

        const response = await axios.post(
            route('absensi.recognize'),
            formData,
            {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            }
        )

        if (response.data?.success) {
            successData.value = response.data

            recognitionMessage.value =
                response.data.message ?? 'Absensi berhasil.'

            addRecentAttendance(response.data)

            activeChallenges.value = []
            currentChallengeIndex.value = 0

            return
        }

        throw new Error(
            response.data?.message ?? 'Absensi gagal.'
        )
    } catch (error) {
        console.error(
            '========== RECOGNITION ERROR =========='
        )
        console.error('Status:', error.response?.status)
        console.error('Response data:', error.response?.data)
        console.error(
            'Response message:',
            error.response?.data?.message
        )
        console.error(
            'Response errors:',
            error.response?.data?.errors
        )
        console.error('Axios error:', error.message)
        console.error(
            '========================================'
        )

        recognitionError.value =
            error.response?.data?.message ||
            error.response?.data?.error ||
            error.response?.data?.errors?.image?.[0] ||
            error.response?.data?.errors?.jadwal_id?.[0] ||
            error.message ||
            'Gagal melakukan absensi.'

        recognitionMessage.value = ''
    } finally {
        recognizing.value = false
    }
}

/*
|--------------------------------------------------------------------------
| RESET ATTENDANCE
|--------------------------------------------------------------------------
*/

const resetAttendance = () => {
    activeChallenges.value = []
    currentChallengeIndex.value = 0

    challengePassed.value = false
    challengeRunning.value = false

    resetFrameState()
    holdFrames.value = 0

    faceSignatureBaseline = null
    noseHistory.value = []

    successData.value = null
    recognitionMessage.value = ''
    recognitionError.value = ''

    if (cameraReady.value && mediaPipeReady.value) {
        startLiveness()
    }
}

/*
|--------------------------------------------------------------------------
| LIVENESS VALID?
|--------------------------------------------------------------------------
*/

const isLivenessValid = computed(() => {
    if (!challengePassed.value) return false
    return true // valid sementara, TTL di server
})

/*
|--------------------------------------------------------------------------
| MOUNT
|--------------------------------------------------------------------------
*/

onMounted(async () => {
    await initFaceLandmarker()
    await startCamera()
})

/*
|--------------------------------------------------------------------------
| UNMOUNT
|--------------------------------------------------------------------------
*/

onBeforeUnmount(() => {
    stopCamera()

    if (faceLandmarker.value) {
        faceLandmarker.value.close()
    }
})
</script>

<template>

    <Head title="Absensi" />

    <div class="space-y-6">

        <!-- =========================================================
             HEADER
        ========================================================== -->

        <div>
            <h1
                class="text-2xl font-bold text-slate-800 dark:text-white"
            >
                Absensi Peserta
            </h1>

            <p
                class="mt-1 text-sm text-slate-500 dark:text-slate-400"
            >
                Absensi otomatis menggunakan pengenalan wajah
                dan verifikasi liveness.
            </p>
        </div>


        <!-- =========================================================
             PILIH JADWAL
        ========================================================== -->

        <div
            v-if="jadwals.length > 0 && !jadwal"
            class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800"
        >

            <label
                class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
            >
                Pilih Jadwal
            </label>

            <select
                v-model="selectedJadwalId"
                @change="resetAttendance"
                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-600 dark:bg-slate-700 dark:text-white"
            >

                <option value="">
                    -- Pilih Jadwal --
                </option>

                <option
                    v-for="item in jadwals"
                    :key="item.id"
                    :value="item.id"
                >
                    {{ formatTanggal(item.tanggal) }}
                    -
                    {{ formatJam(item.jam_mulai) }}
                    -
                    {{ item.kegiatan }}
                </option>

            </select>

        </div>


        <!-- =========================================================
             INFO JADWAL
        ========================================================== -->

        <div
            v-if="selectedJadwal"
            class="grid grid-cols-1 gap-4 md:grid-cols-4"
        >

            <!-- PROGRAM -->

            <div
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800"
            >

                <p
                    class="text-xs font-medium uppercase tracking-wide text-slate-400"
                >
                    Pelatihan
                </p>

                <p
                    class="mt-2 font-semibold text-slate-800 dark:text-white"
                >
                    {{ namaProgram }}
                </p>

            </div>


            <!-- TAHAP -->

            <div
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800"
            >

                <p
                    class="text-xs font-medium uppercase tracking-wide text-slate-400"
                >
                    Tahap
                </p>

                <p
                    class="mt-2 font-semibold text-slate-800 dark:text-white"
                >
                    {{ namaTahap }}
                </p>

            </div>


            <!-- TANGGAL -->

            <div
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800"
            >

                <p
                    class="text-xs font-medium uppercase tracking-wide text-slate-400"
                >
                    Tanggal
                </p>

                <p
                    class="mt-2 font-semibold text-slate-800 dark:text-white"
                >
                    {{ formatTanggal(selectedJadwal.tanggal) }}
                </p>

            </div>


            <!-- WAKTU -->

            <div
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800"
            >

                <p
                    class="text-xs font-medium uppercase tracking-wide text-slate-400"
                >
                    Waktu
                </p>

                <p
                    class="mt-2 font-semibold text-slate-800 dark:text-white"
                >
                    {{ formatJam(selectedJadwal.jam_mulai) }}
                    -
                    {{ formatJam(selectedJadwal.jam_selesai) }}
                </p>

            </div>

        </div>


        <!-- =========================================================
             MAIN
        ========================================================== -->

        <div
            class="grid grid-cols-1 gap-6 lg:grid-cols-3"
        >

            <!-- =====================================================
                 CAMERA
            ====================================================== -->

            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2 dark:border-slate-700 dark:bg-slate-800"
            >

                <!-- HEADER CAMERA -->

                <div
                    class="border-b border-slate-200 px-5 py-4 dark:border-slate-700"
                >

                    <div
                        class="flex items-center justify-between"
                    >

                        <div>

                            <h2
                                class="font-semibold text-slate-800 dark:text-white"
                            >
                                Kamera Absensi
                            </h2>

                            <p
                                class="mt-1 text-xs text-slate-500 dark:text-slate-400"
                            >
                                Pastikan wajah terlihat jelas.
                            </p>

                        </div>


                        <!-- CAMERA STATUS -->

                        <div
                            class="flex items-center gap-2"
                        >

                            <span
                                class="h-2.5 w-2.5 rounded-full"
                                :class="
                                    cameraReady
                                        ? 'bg-emerald-500'
                                        : 'bg-red-500'
                                "
                            ></span>

                            <span
                                class="text-xs font-medium text-slate-500 dark:text-slate-400"
                            >
                                {{
                                    cameraReady
                                        ? 'Kamera aktif'
                                        : 'Kamera tidak aktif'
                                }}
                            </span>

                        </div>

                    </div>

                </div>


                <div class="p-5">

                    <!-- VIDEO -->

                    <div
                        class="relative mx-auto aspect-video max-w-3xl overflow-hidden rounded-2xl bg-slate-950"
                    >

                        <video
                            ref="video"
                            autoplay
                            muted
                            playsinline
                            class="h-full w-full object-cover"
                        ></video>


                        <!-- FRAME WAJAH -->

                        <div
                            class="pointer-events-none absolute inset-0 flex items-center justify-center"
                        >

                            <div
                                class="h-[65%] w-[45%] rounded-[50%] border-2 transition"
                                :class="
                                    challengePassed
                                        ? 'border-emerald-400'
                                        : challengeRunning
                                            ? 'border-indigo-400'
                                            : 'border-white/70'
                                "
                            ></div>

                        </div>


                        <!-- CHALLENGE -->

                        <div
                            v-if="challengeRunning && currentChallenge"
                            class="absolute left-1/2 top-5 -translate-x-1/2"
                        >

                            <div
                                class="rounded-2xl bg-indigo-600 px-5 py-3 text-center shadow-lg"
                            >

                                <p
                                    class="text-[10px] font-semibold uppercase tracking-wider text-white/70"
                                >
                                    Challenge
                                    {{ currentChallengeIndex + 1 }}/{{ activeChallenges.length }}
                                </p>

                                <p
                                    class="mt-1 text-sm font-bold text-white"
                                >
                                    {{ currentChallenge.instruction }}
                                </p>


                                <!-- HOLD PROGRESS BAR -->

                                <div
                                    v-if="holdFrames > 0"
                                    class="mx-auto mt-2 h-1.5 w-40 overflow-hidden rounded-full bg-white/20"
                                >

                                    <div
                                        class="h-full bg-emerald-400 transition-all"
                                        :style="{
                                            width:
                                                (holdFrames / HOLD_FRAMES) * 100 +
                                                '%',
                                        }"
                                    ></div>

                                </div>

                            </div>

                        </div>


                        <!-- LIVENESS BERHASIL -->

                        <div
                            v-if="challengePassed && !recognizing"
                            class="absolute left-1/2 bottom-5 -translate-x-1/2"
                        >

                            <div
                                class="rounded-full bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg"
                            >
                                ✓ Liveness berhasil
                            </div>

                        </div>


                        <!-- RECOGNITION PROCESS -->

                        <div
                            v-if="recognizing"
                            class="absolute inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm"
                        >

                            <div
                                class="rounded-2xl bg-white px-6 py-5 text-center shadow-xl dark:bg-slate-800"
                            >

                                <div
                                    class="mx-auto mb-3 h-8 w-8 animate-spin rounded-full border-4 border-slate-200 border-t-indigo-600"
                                ></div>

                                <p
                                    class="text-sm font-semibold text-slate-800 dark:text-white"
                                >
                                    {{ recognitionMessage }}
                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- CANVAS -->

                    <canvas
                        ref="canvas"
                        class="hidden"
                    ></canvas>


                    <!-- =============================================
                         CHALLENGE PROGRESS DOTS
                    ============================================== -->

                    <div
                        v-if="activeChallenges.length > 0"
                        class="mt-5 flex items-center justify-center gap-2"
                    >

                        <template
                            v-for="(ch, i) in activeChallenges"
                            :key="i"
                        >

                            <div
                                class="flex items-center gap-2"
                            >

                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold transition"
                                    :class="{
                                        'bg-emerald-500 text-white':
                                            i < currentChallengeIndex ||
                                            challengePassed,
                                        'bg-indigo-500 text-white ring-2 ring-indigo-300':
                                            i === currentChallengeIndex &&
                                            challengeRunning,
                                        'bg-slate-200 text-slate-500 dark:bg-slate-700 dark:text-slate-400':
                                            i > currentChallengeIndex &&
                                            !challengePassed,
                                    }"
                                >

                                    <span
                                        v-if="
                                            i < currentChallengeIndex ||
                                            challengePassed
                                        "
                                    >
                                        ✓
                                    </span>

                                    <span v-else>
                                        {{ i + 1 }}
                                    </span>

                                </div>


                                <div
                                    v-if="i < activeChallenges.length - 1"
                                    class="h-0.5 w-6"
                                    :class="
                                        i < currentChallengeIndex ||
                                        challengePassed
                                            ? 'bg-emerald-500'
                                            : 'bg-slate-300 dark:bg-slate-700'
                                    "
                                ></div>

                            </div>

                        </template>

                    </div>


                    <!-- =============================================
                         DEBUG POSE (opsional, bisa dihapus)
                    ============================================== -->

                    <div
                        v-if="challengeRunning && currentChallenge"
                        class="mt-3 flex items-center justify-center gap-3 text-[10px] text-slate-400 dark:text-slate-500"
                    >
                        <span>Yaw: {{ yaw.toFixed(3) }}</span>
                        <span>Pitch: {{ pitch.toFixed(3) }}</span>
                    </div>


                    <!-- ERRORS -->

                    <div
                        v-if="cameraError"
                        class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-300"
                    >
                        {{ cameraError }}
                    </div>

                    <div
                        v-if="mediaPipeError"
                        class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-300"
                    >
                        {{ mediaPipeError }}
                    </div>

                    <div
                        v-if="recognitionError"
                        class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-300"
                    >
                        {{ recognitionError }}
                    </div>


                    <!-- BUTTON -->

                    <div
                        class="mt-5 flex justify-center"
                    >

                        <button
                            v-if="!successData"
                            type="button"
                            :disabled="
                                !cameraReady ||
                                !mediaPipeReady ||
                                recognizing ||
                                challengeRunning ||
                                !selectedJadwalId
                            "
                            @click="startLiveness"
                            class="rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50"
                        >

                            {{
                                challengeRunning
                                    ? 'Verifikasi sedang berjalan...'
                                    : 'Mulai Verifikasi Liveness'
                            }}

                        </button>


                        <button
                            v-else
                            type="button"
                            @click="resetAttendance"
                            class="rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
                        >
                            Absensi Berikutnya
                        </button>

                    </div>

                </div>

            </div>


            <!-- =====================================================
                 ABSENSI TERBARU
            ====================================================== -->

            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800"
            >

                <!-- HEADER -->

                <div
                    class="border-b border-slate-200 px-5 py-4 dark:border-slate-700"
                >

                    <div
                        class="flex items-center justify-between"
                    >

                        <div>

                            <h2
                                class="font-semibold text-slate-800 dark:text-white"
                            >
                                Absensi Terbaru
                            </h2>

                            <p
                                class="mt-1 text-xs text-slate-500 dark:text-slate-400"
                            >
                                5 peserta terakhir
                            </p>

                        </div>


                        <span
                            class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-300"
                        >
                            {{ recentAttendances.length }} / 5
                        </span>

                    </div>

                </div>


                <!-- BELUM ADA DATA -->

                <div
                    v-if="recentAttendances.length === 0"
                    class="flex min-h-[350px] items-center justify-center px-5"
                >

                    <div class="text-center">

                        <div
                            class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-700"
                        >

                            <svg
                                class="h-7 w-7 text-slate-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 14a5 5 0 100-10 5 5 0 000 10zm-7 8a7 7 0 0114 0"
                                />
                            </svg>

                        </div>

                        <p
                            class="text-sm font-semibold text-slate-600 dark:text-slate-300"
                        >
                            Belum ada absensi
                        </p>

                        <p
                            class="mt-1 text-xs text-slate-400"
                        >
                            Peserta yang berhasil absen akan muncul di sini.
                        </p>

                    </div>

                </div>


                <!-- LIST 5 ABSENSI -->

                <div
                    v-else
                    class="divide-y divide-slate-100 dark:divide-slate-700"
                >

                    <div
                        v-for="(item, index) in recentAttendances"
                        :key="
                            normalizeAttendance(item)?.absensi?.id ??
                            index
                        "
                        class="px-4 py-4 transition hover:bg-slate-50 dark:hover:bg-slate-700/30"
                    >

                        <div
                            class="flex items-center gap-3"
                        >

                            <!-- NOMOR -->

                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-sm font-bold text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400"
                            >
                                {{ index + 1 }}
                            </div>


                            <!-- DATA -->

                            <div
                                class="min-w-0 flex-1"
                            >

                                <!-- NAMA + STATUS -->

                                <div
                                    class="flex items-center justify-between gap-2"
                                >

                                    <p
                                        class="truncate text-sm font-bold text-slate-800 dark:text-white"
                                    >
                                        {{
                                            normalizeAttendance(item)
                                                ?.peserta
                                                ?.nama ??
                                            '-'
                                        }}
                                    </p>

                                    <span
                                        class="shrink-0 text-[10px] font-bold text-emerald-600 dark:text-emerald-400"
                                    >
                                        HADIR
                                    </span>

                                </div>


                                <!-- PELATIHAN -->

                                <p
                                    class="mt-1 truncate text-xs text-slate-500 dark:text-slate-400"
                                >

                                    {{
                                        normalizeAttendance(item)
                                            ?.program_pelatihan
                                            ?.pelatihan ??
                                        '-'
                                    }}

                                    <span class="mx-1">
                                        •
                                    </span>

                                    {{
                                        normalizeAttendance(item)
                                            ?.program_pelatihan
                                            ?.tahap ??
                                        '-'
                                    }}

                                    <span class="mx-1">
                                        •
                                    </span>

                                    {{
                                        normalizeAttendance(item)
                                            ?.kelas
                                            ?.nama ??
                                        '-'
                                    }}

                                </p>


                                <!-- WAKTU -->

                                <div
                                    class="mt-1 flex items-center gap-2 text-[11px] text-slate-400"
                                >

                                    <span>
                                        Ptm.
                                        {{
                                            normalizeAttendance(item)
                                                ?.jadwal
                                                ?.pertemuan ??
                                            '-'
                                        }}
                                    </span>

                                    <span>
                                        •
                                    </span>

                                    <span>
                                        {{
                                            formatTime(
                                                normalizeAttendance(item)
                                                    ?.absensi
                                                    ?.waktu_masuk
                                            )
                                        }}
                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- =========================================================
             HASIL ABSENSI TERAKHIR
        ========================================================== -->

        <div
            v-if="successData"
            class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-900/50 dark:bg-emerald-950/20"
        >

            <div
                class="flex items-center gap-3"
            >

                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400"
                >
                    ✓
                </div>


                <div class="min-w-0">

                    <p
                        class="text-sm font-bold text-emerald-700 dark:text-emerald-300"
                    >
                        Absensi berhasil
                    </p>

                    <p
                        class="truncate text-sm text-emerald-700 dark:text-emerald-300"
                    >
                        {{ successData.peserta?.nama ?? '-' }}

                        <span class="mx-1">
                            •
                        </span>

                        {{
                            successData.program_pelatihan
                                ?.pelatihan ??
                            '-'
                        }}

                        <span class="mx-1">
                            •
                        </span>

                        {{
                            successData.kelas?.nama ??
                            '-'
                        }}

                        <span class="mx-1">
                            •
                        </span>

                        {{
                            formatTime(
                                successData.absensi
                                    ?.waktu_masuk
                            )
                        }}
                    </p>

                </div>

            </div>

        </div>

    </div>

</template>