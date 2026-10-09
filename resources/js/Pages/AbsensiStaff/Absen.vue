<script setup>
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import {
    computed,
    nextTick,
    onBeforeUnmount,
    onMounted,
    ref,
} from 'vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    user: {
        type: Object,
        default: () => ({}),
    },

    lokasi: {
        type: Array,
        default: () => [],
    },

    absensiHariIni: {
        type: Object,
        default: null,
    },

    jadwal: {
        type: Object,
        default: null,
    },

    libur: {
        type: Boolean,
        default: false,
    },

    alasanLibur: {
        type: String,
        default: null,
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
| GPS
|--------------------------------------------------------------------------
*/

const latitude = ref(null)
const longitude = ref(null)
const gpsAccuracy = ref(null)

const gpsLoading = ref(false)
const gpsError = ref('')

/*
|--------------------------------------------------------------------------
| LOKASI
|--------------------------------------------------------------------------
*/

const selectedLokasi = ref(null)
const distance = ref(null)

/*
|--------------------------------------------------------------------------
| ABSENSI
|--------------------------------------------------------------------------
*/

const processing = ref(false)
const processingType = ref(null)

const message = ref('')
const errorMessage = ref('')

/*
|--------------------------------------------------------------------------
| LIVENESS — CORE
|--------------------------------------------------------------------------
*/

let faceLandmarker = null

const landmarkerReady = ref(false)
const faceDetected = ref(false)

const blinkState = ref('open')
const blinkClosedFrames = ref(0)
const blinkOpenFrames = ref(0)

const livenessPassed = ref(false)
const livenessPassedAt = ref(null)

/*
|--------------------------------------------------------------------------
| LIVENESS — RANDOM CHALLENGE
|--------------------------------------------------------------------------
*/

const ALL_CHALLENGES = [
    'blink',
    'turn_left',
    'turn_right',
    'look_up',
    'look_down',
]

const activeChallenges = ref([])
const currentChallengeIndex = ref(0)
const challengeHoldFrames = ref(0)

const currentChallenge = computed(() => {
    return activeChallenges.value[currentChallengeIndex.value] ?? null
})

const livenessMessage = ref('Menyiapkan challenge...')

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
| LIVENESS — MICRO MOVEMENT (ANTI FOTO STATIS)
|--------------------------------------------------------------------------
*/

const noseHistory = ref([])
const MAX_NOSE_HISTORY = 30

/*
|--------------------------------------------------------------------------
| LIVENESS — FACE SIGNATURE (ANTI FACE SWAP)
|--------------------------------------------------------------------------
*/

let faceSignatureBaseline = null

/*
|--------------------------------------------------------------------------
| LIVENESS — THRESHOLD
|--------------------------------------------------------------------------
*/

const BLINK_CLOSE_THRESHOLD = 0.17
const BLINK_OPEN_THRESHOLD = 0.28

const REQUIRED_CLOSED_FRAMES = 3
const REQUIRED_OPEN_FRAMES = 3

const MIN_BLINK_DURATION = 80
const MAX_BLINK_DURATION = 800

const HEAD_YAW_THRESHOLD = 0.10
const HEAD_PITCH_THRESHOLD = 0.06

const CHALLENGE_HOLD_FRAMES = 5

const LIVENESS_TTL = 30000 // 30 detik

const MIN_MOVEMENT_VARIANCE = 0.00002

let blinkStartTime = null

/*
|--------------------------------------------------------------------------
| TIMER
|--------------------------------------------------------------------------
*/

const currentTime = ref(new Date())

let timerInterval = null
let faceAnimationFrame = null

/*
|--------------------------------------------------------------------------
| JADWAL
|--------------------------------------------------------------------------
*/

const jamMasuk = computed(() => {
    return props.jadwal?.jam_masuk ?? null
})

const jamPulang = computed(() => {
    return props.jadwal?.jam_pulang ?? null
})

const toleransi = computed(() => {
    return Number(props.jadwal?.toleransi_menit ?? 0)
})

/*
|--------------------------------------------------------------------------
| FORMAT JAM
|--------------------------------------------------------------------------
*/

const formatJam = (value) => {
    if (!value) {
        return '-'
    }

    return String(value).substring(0, 5)
}

/*
|--------------------------------------------------------------------------
| STATUS WAKTU
|--------------------------------------------------------------------------
*/

const timeStatus = computed(() => {
    if (props.libur) {
        return {
            type: 'libur',
            label: 'Hari Libur',
            description:
                props.alasanLibur ||
                'Absensi tidak tersedia hari ini.',
        }
    }

    if (!props.jadwal) {
        return {
            type: 'none',
            label: 'Tidak Ada Jadwal',
            description:
                'Tidak terdapat jadwal staff aktif hari ini.',
        }
    }

    const now = currentTime.value

    const masukTime = formatJam(jamMasuk.value)
    const pulangTime = formatJam(jamPulang.value)

    const [masukHour, masukMinute] = masukTime.split(':').map(Number)
    const [pulangHour, pulangMinute] = pulangTime.split(':').map(Number)

    const masuk = new Date(now)
    masuk.setHours(masukHour, masukMinute, 0, 0)

    const pulang = new Date(now)
    pulang.setHours(pulangHour, pulangMinute, 0, 0)

    const batasTerlambat = new Date(masuk)
    batasTerlambat.setMinutes(
        batasTerlambat.getMinutes() + toleransi.value
    )

    if (now < masuk) {
        return {
            type: 'belum-masuk',
            label: 'Belum Waktu Absensi',
            description: `Absensi masuk dibuka pukul ${formatJam(
                jamMasuk.value
            )}.`,
        }
    }

    if (now <= batasTerlambat) {
        return {
            type: 'masuk',
            label: 'Absensi Masuk Dibuka',
            description:
                toleransi.value > 0
                    ? `Silakan absen masuk. Toleransi ${toleransi.value} menit.`
                    : 'Silakan lakukan absensi masuk.',
        }
    }

    if (now <= pulang) {
        return {
            type: 'terlambat',
            label: 'Absensi Masuk Terlambat',
            description:
                'Waktu toleransi telah lewat. Absensi tetap dapat dilakukan sebagai terlambat.',
        }
    }

    return {
        type: 'pulang',
        label: 'Absensi Pulang Dibuka',
        description: `Jam pulang telah lewat. Silakan absen pulang setelah pukul ${formatJam(
            jamPulang.value
        )}.`,
    }
})

/*
|--------------------------------------------------------------------------
| ABSENSI HARI INI
|--------------------------------------------------------------------------
*/

const sudahMasuk = computed(() => {
    return !!props.absensiHariIni?.waktu_masuk
})

const sudahPulang = computed(() => {
    return !!props.absensiHariIni?.waktu_keluar
})

/*
|--------------------------------------------------------------------------
| BOLEH ABSEN MASUK
|--------------------------------------------------------------------------
*/

const canAbsenMasuk = computed(() => {
    if (processing.value) return false
    if (props.libur) return false
    if (!props.jadwal) return false
    if (sudahMasuk.value) return false

    return (
        timeStatus.value.type === 'masuk' ||
        timeStatus.value.type === 'terlambat'
    )
})

/*
|--------------------------------------------------------------------------
| BOLEH ABSEN PULANG
|--------------------------------------------------------------------------
*/

const canAbsenPulang = computed(() => {
    if (processing.value) return false
    if (props.libur) return false
    if (!props.jadwal) return false
    if (!sudahMasuk.value) return false
    if (sudahPulang.value) return false

    return timeStatus.value.type === 'pulang'
})

/*
|--------------------------------------------------------------------------
| LOKASI READY
|--------------------------------------------------------------------------
*/

const locationReady = computed(() => {
    return (
        latitude.value !== null &&
        longitude.value !== null &&
        selectedLokasi.value !== null
    )
})

/*
|--------------------------------------------------------------------------
| HAVERSINE
|--------------------------------------------------------------------------
*/

const calculateDistance = (lat1, lon1, lat2, lon2) => {
    const earthRadius = 6371000

    const toRadians = (value) => (value * Math.PI) / 180

    const dLat = toRadians(lat2 - lat1)
    const dLon = toRadians(lon2 - lon1)

    const a =
        Math.sin(dLat / 2) ** 2 +
        Math.cos(toRadians(lat1)) *
            Math.cos(toRadians(lat2)) *
            Math.sin(dLon / 2) ** 2

    const c =
        2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a))

    return earthRadius * c
}

/*
|--------------------------------------------------------------------------
| CARI LOKASI TERDEKAT
|--------------------------------------------------------------------------
*/

const findNearestLocation = () => {
    if (
        latitude.value === null ||
        longitude.value === null
    ) {
        return
    }

    let nearest = null
    let nearestDistance = Infinity

    for (const lokasiItem of props.lokasi) {
        if (lokasiItem.status !== 'aktif') continue

        const distanceValue = calculateDistance(
            Number(latitude.value),
            Number(longitude.value),
            Number(lokasiItem.latitude),
            Number(lokasiItem.longitude)
        )

        if (distanceValue < nearestDistance) {
            nearestDistance = distanceValue
            nearest = lokasiItem
        }
    }

    selectedLokasi.value = nearest
    distance.value = nearest ? nearestDistance : null
}

/*
|--------------------------------------------------------------------------
| FORMAT JARAK
|--------------------------------------------------------------------------
*/

const formatDistance = (value) => {
    if (value === null || value === undefined) {
        return '-'
    }

    if (value < 1000) {
        return `${Math.round(value)} meter`
    }

    return `${(value / 1000).toFixed(2)} km`
}

/*
|--------------------------------------------------------------------------
| CEK RADIUS
|--------------------------------------------------------------------------
*/

const insideRadius = computed(() => {
    if (!selectedLokasi.value || distance.value === null) {
        return false
    }

    return (
        distance.value <= Number(selectedLokasi.value.radius)
    )
})

/*
|--------------------------------------------------------------------------
| GPS
|--------------------------------------------------------------------------
*/

const getLocation = () => {
    gpsLoading.value = true
    gpsError.value = ''

    if (!navigator.geolocation) {
        gpsLoading.value = false
        gpsError.value = 'Browser tidak mendukung GPS.'
        return
    }

    navigator.geolocation.getCurrentPosition(
        (position) => {
            latitude.value = position.coords.latitude
            longitude.value = position.coords.longitude
            gpsAccuracy.value = position.coords.accuracy
            gpsLoading.value = false

            findNearestLocation()
        },
        (error) => {
            gpsLoading.value = false

            switch (error.code) {
                case error.PERMISSION_DENIED:
                    gpsError.value =
                        'Izin lokasi ditolak. Silakan aktifkan lokasi pada browser.'
                    break
                case error.POSITION_UNAVAILABLE:
                    gpsError.value = 'Lokasi tidak tersedia.'
                    break
                case error.TIMEOUT:
                    gpsError.value =
                        'Pengambilan lokasi terlalu lama.'
                    break
                default:
                    gpsError.value = 'Gagal mendapatkan lokasi.'
            }
        },
        {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 0,
        }
    )
}

/*
|--------------------------------------------------------------------------
| START CAMERA
|--------------------------------------------------------------------------
*/

const startCamera = async () => {
    cameraError.value = ''

    if (
        !navigator.mediaDevices ||
        !navigator.mediaDevices.getUserMedia
    ) {
        cameraError.value = 'Browser tidak mendukung kamera.'
        return
    }

    try {
        const cameraStream =
            await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'user',
                    width: { ideal: 1280 },
                    height: { ideal: 720 },
                },
                audio: false,
            })

        stream.value = cameraStream

        await nextTick()

        if (!video.value) {
            throw new Error('Elemen video tidak ditemukan.')
        }

        video.value.srcObject = cameraStream
        await video.value.play()

        cameraReady.value = true
    } catch (error) {
        console.error('Camera error:', error)
        cameraError.value =
            'Kamera tidak dapat digunakan. Pastikan izin kamera telah diberikan.'
    }
}

/*
|--------------------------------------------------------------------------
| STOP CAMERA
|--------------------------------------------------------------------------
*/

const stopCamera = () => {
    if (stream.value) {
        stream.value.getTracks().forEach((track) => {
            track.stop()
        })
        stream.value = null
    }

    if (video.value) {
        video.value.srcObject = null
    }

    cameraReady.value = false
}

/*
|--------------------------------------------------------------------------
| INITIALIZE MEDIAPIPE
|--------------------------------------------------------------------------
*/

const initializeFaceLandmarker = async () => {
    try {
        const { FaceLandmarker, FilesetResolver } = await import(
            '@mediapipe/tasks-vision'
        )

        const vision =
            await FilesetResolver.forVisionTasks('/mediapipe/wasm')

        faceLandmarker =
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

        landmarkerReady.value = true

        generateChallenges()

        startFaceDetection()

        console.log('✅ MediaPipe FaceLandmarker siap')
    } catch (error) {
        console.error('❌ MediaPipe error:', error)
        landmarkerReady.value = false
        livenessMessage.value = 'Sistem liveness gagal dimuat.'
    }
}

/*
|--------------------------------------------------------------------------
| POINT DISTANCE
|--------------------------------------------------------------------------
*/

const pointDistance = (a, b) => {
    const dx = a.x - b.x
    const dy = a.y - b.y
    return Math.sqrt(dx * dx + dy * dy)
}

/*
|--------------------------------------------------------------------------
| CALCULATE EAR
|--------------------------------------------------------------------------
*/

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

    const vertical1 = pointDistance(p2, p6)
    const vertical2 = pointDistance(p3, p5)
    const horizontal = pointDistance(p1, p4)

    if (horizontal <= 0) return 0

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
| CHALLENGE DESCRIPTION
|--------------------------------------------------------------------------
*/

const describeChallenge = (challenge) => {
    switch (challenge) {
        case 'blink':
            return 'Kedipkan mata Anda'
        case 'turn_left':
            return 'Tengok ke KIRI'
        case 'turn_right':
            return 'Tengok ke KANAN'
        case 'look_up':
            return 'Lihat ke ATAS'
        case 'look_down':
            return 'Lihat ke BAWAH'
        default:
            return 'Ikuti instruksi'
    }
}

/*
|--------------------------------------------------------------------------
| GENERATE CHALLENGES (RANDOM 3 DARI 5)
|--------------------------------------------------------------------------
*/

const generateChallenges = () => {
    const shuffled = [...ALL_CHALLENGES].sort(
        () => Math.random() - 0.5
    )

    activeChallenges.value = shuffled.slice(0, 3)

    currentChallengeIndex.value = 0
    challengeHoldFrames.value = 0

    livenessPassed.value = false
    livenessPassedAt.value = null

    blinkState.value = 'open'
    blinkClosedFrames.value = 0
    blinkOpenFrames.value = 0
    blinkStartTime = null

    faceSignatureBaseline = null
    noseHistory.value = []

    livenessMessage.value = `Challenge 1/3: ${describeChallenge(
        activeChallenges.value[0]
    )}`

    console.log('🎲 Challenge:', activeChallenges.value)
}

/*
|--------------------------------------------------------------------------
| NEXT CHALLENGE
|--------------------------------------------------------------------------
*/

const nextChallenge = () => {
    challengeHoldFrames.value = 0
    blinkState.value = 'open'
    blinkClosedFrames.value = 0
    blinkOpenFrames.value = 0
    blinkStartTime = null

    currentChallengeIndex.value++

    if (currentChallengeIndex.value >= activeChallenges.value.length) {
        livenessPassed.value = true
        livenessPassedAt.value = Date.now()

        livenessMessage.value =
            '✓ Liveness berhasil. Semua challenge selesai.'

        console.log('✅ LIVENESS BERHASIL')
        return
    }

    livenessMessage.value = `Challenge ${
        currentChallengeIndex.value + 1
    }/${activeChallenges.value.length}: ${describeChallenge(
        currentChallenge.value
    )}`

    console.log('➡️ Next challenge:', currentChallenge.value)
}

/*
|--------------------------------------------------------------------------
| PROCESS BLINK CHALLENGE
|--------------------------------------------------------------------------
*/

const processBlinkChallenge = (landmarks) => {
    const leftEyeIndices = [33, 160, 158, 133, 153, 144]
    const rightEyeIndices = [362, 385, 387, 263, 373, 380]

    const leftEAR = calculateEAR(landmarks, leftEyeIndices)
    const rightEAR = calculateEAR(landmarks, rightEyeIndices)

    const ear = (leftEAR + rightEAR) / 2

    if (!ear || !Number.isFinite(ear)) return

    if (blinkState.value === 'open') {
        if (ear < BLINK_CLOSE_THRESHOLD) {
            if (!blinkStartTime) {
                blinkStartTime = performance.now()
            }

            blinkClosedFrames.value++
            blinkOpenFrames.value = 0

            if (blinkClosedFrames.value >= REQUIRED_CLOSED_FRAMES) {
                blinkState.value = 'closed'
                blinkClosedFrames.value = 0
                livenessMessage.value =
                    'Mata tertutup ✓ Buka kembali mata.'
            }
        } else {
            blinkClosedFrames.value = 0
            blinkStartTime = null
        }
        return
    }

    if (blinkState.value === 'closed') {
        if (ear > BLINK_OPEN_THRESHOLD) {
            blinkOpenFrames.value++

            if (blinkOpenFrames.value >= REQUIRED_OPEN_FRAMES) {
                const duration = blinkStartTime
                    ? performance.now() - blinkStartTime
                    : 0

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
                    return
                }

                if (getMovementVariance() < MIN_MOVEMENT_VARIANCE) {
                    livenessMessage.value =
                        '⚠️ Terdeteksi gambar statis. Gunakan wajah asli.'
                    return
                }

                console.log('✅ Blink challenge passed')
                nextChallenge()
            }
        } else {
            blinkOpenFrames.value = 0
        }
    }
}

/*
|--------------------------------------------------------------------------
| PROCESS POSE CHALLENGE
|--------------------------------------------------------------------------
*/

const processPoseChallenge = (isConditionMet, hint) => {
    if (isConditionMet) {
        challengeHoldFrames.value++

        livenessMessage.value = `✓ Tahan... ${challengeHoldFrames.value}/${CHALLENGE_HOLD_FRAMES}`

        if (challengeHoldFrames.value >= CHALLENGE_HOLD_FRAMES) {
            console.log(
                '✅ Pose challenge passed:',
                currentChallenge.value
            )
            nextChallenge()
        }
    } else {
        challengeHoldFrames.value = 0

        livenessMessage.value =
            describeChallenge(currentChallenge.value) +
            (hint ? ` (${hint})` : '')
    }
}

/*
|--------------------------------------------------------------------------
| PROCESS CHALLENGE
|--------------------------------------------------------------------------
*/

const processChallenge = (landmarks, pose) => {
    if (livenessPassed.value) return

    const challenge = currentChallenge.value
    if (!challenge) return

    if (!isFaceConsistent(landmarks)) {
        resetLiveness()
        generateChallenges()
        livenessMessage.value =
            '⚠️ Wajah berubah. Silakan ulangi dari awal.'
        return
    }

    updateMicroMovement(landmarks)

    yaw.value = pose.yaw
    pitch.value = pose.pitch
    roll.value = pose.roll

    switch (challenge) {
        case 'blink':
            processBlinkChallenge(landmarks)
            break
        case 'turn_left':
            processPoseChallenge(
                pose.yaw > HEAD_YAW_THRESHOLD,
                'Tengok lebih ke KIRI'
            )
            break
        case 'turn_right':
            processPoseChallenge(
                pose.yaw < -HEAD_YAW_THRESHOLD,
                'Tengok lebih ke KANAN'
            )
            break
        case 'look_up':
            processPoseChallenge(
                pose.pitch > HEAD_PITCH_THRESHOLD,
                'Lihat lebih ke ATAS'
            )
            break
        case 'look_down':
            processPoseChallenge(
                pose.pitch < -HEAD_PITCH_THRESHOLD,
                'Lihat lebih ke BAWAH'
            )
            break
    }
}

/*
|--------------------------------------------------------------------------
| FACE DETECTION LOOP
|--------------------------------------------------------------------------
*/

const detectFace = () => {
    if (
        !faceLandmarker ||
        !video.value ||
        !cameraReady.value
    ) {
        faceAnimationFrame = requestAnimationFrame(detectFace)
        return
    }

    if (
        video.value.readyState >= 2 &&
        video.value.videoWidth > 0 &&
        video.value.videoHeight > 0
    ) {
        try {
            const result = faceLandmarker.detectForVideo(
                video.value,
                performance.now()
            )

            const allFaces = result?.faceLandmarks ?? []

            // Deteksi multiple faces
            if (allFaces.length > 1) {
                faceDetected.value = false

                if (!livenessPassed.value) {
                    livenessMessage.value =
                        '⚠️ Hanya boleh 1 wajah di frame.'
                    blinkClosedFrames.value = 0
                    blinkOpenFrames.value = 0
                    challengeHoldFrames.value = 0
                }
            } else {
                const landmarks = allFaces[0]

                if (!landmarks || landmarks.length === 0) {
                    faceDetected.value = false

                    if (!livenessPassed.value) {
                        livenessMessage.value =
                            'Wajah tidak terdeteksi. Posisikan wajah di dalam frame.'
                        blinkClosedFrames.value = 0
                        blinkOpenFrames.value = 0
                        challengeHoldFrames.value = 0
                    }
                } else {
                    faceDetected.value = true

                    if (!livenessPassed.value) {
                        const pose = calculateHeadPose(landmarks)
                        processChallenge(landmarks, pose)
                    }
                }
            }
        } catch (error) {
            console.error('❌ Face detection error:', error)
        }
    }

    faceAnimationFrame = requestAnimationFrame(detectFace)
}

/*
|--------------------------------------------------------------------------
| START FACE DETECTION
|--------------------------------------------------------------------------
*/

const startFaceDetection = () => {
    if (faceAnimationFrame) {
        cancelAnimationFrame(faceAnimationFrame)
    }
    faceAnimationFrame = requestAnimationFrame(detectFace)
}

/*
|--------------------------------------------------------------------------
| STOP FACE DETECTION
|--------------------------------------------------------------------------
*/

const stopFaceDetection = () => {
    if (faceAnimationFrame) {
        cancelAnimationFrame(faceAnimationFrame)
        faceAnimationFrame = null
    }
}

/*
|--------------------------------------------------------------------------
| CAPTURE IMAGE
|--------------------------------------------------------------------------
*/

const captureImage = () => {
    if (!video.value || !canvas.value || !cameraReady.value) {
        return null
    }

    const width = video.value.videoWidth
    const height = video.value.videoHeight

    if (!width || !height) return null

    canvas.value.width = width
    canvas.value.height = height

    const context = canvas.value.getContext('2d')
    if (!context) return null

    context.save()
    context.translate(width, 0)
    context.scale(-1, 1)
    context.drawImage(video.value, 0, 0, width, height)
    context.restore()

    // Watermark
    context.fillStyle = 'rgba(255, 255, 255, 0.85)'
    context.font = 'bold 20px monospace'
    context.fillText(
        `TIME: ${new Date().toISOString()}`,
        20,
        height - 50
    )
    context.fillText(
        `CHALLENGE: ${activeChallenges.value.join(',')}`,
        20,
        height - 20
    )

    return canvas.value.toDataURL('image/jpeg', 0.9)
}

/*
|--------------------------------------------------------------------------
| DATA URL -> BLOB
|--------------------------------------------------------------------------
*/

const dataUrlToBlob = (dataUrl) => {
    const parts = dataUrl.split(',')
    const mime =
        parts[0].match(/:(.*?);/)?.[1] || 'image/jpeg'
    const binary = atob(parts[1])
    const array = new Uint8Array(binary.length)

    for (let index = 0; index < binary.length; index++) {
        array[index] = binary.charCodeAt(index)
    }

    return new Blob([array], { type: mime })
}

/*
|--------------------------------------------------------------------------
| RESET MESSAGE
|--------------------------------------------------------------------------
*/

const resetMessage = () => {
    message.value = ''
    errorMessage.value = ''
}

/*
|--------------------------------------------------------------------------
| RESET LIVENESS
|--------------------------------------------------------------------------
*/

const resetLiveness = () => {
    livenessPassed.value = false
    livenessPassedAt.value = null

    activeChallenges.value = []
    currentChallengeIndex.value = 0
    challengeHoldFrames.value = 0

    blinkState.value = 'open'
    blinkClosedFrames.value = 0
    blinkOpenFrames.value = 0
    blinkStartTime = null

    faceSignatureBaseline = null
    noseHistory.value = []

    yaw.value = 0
    pitch.value = 0
    roll.value = 0

    livenessMessage.value = 'Memuat challenge...'
}

/*
|--------------------------------------------------------------------------
| LIVENESS VALID?
|--------------------------------------------------------------------------
*/

const isLivenessValid = computed(() => {
    if (!livenessPassed.value || !livenessPassedAt.value) {
        return false
    }

    return Date.now() - livenessPassedAt.value < LIVENESS_TTL
})

/*
|--------------------------------------------------------------------------
| SUBMIT ATTENDANCE
|--------------------------------------------------------------------------
*/

const submitAttendance = async (type) => {
    resetMessage()

    if (processing.value) return

    if (type === 'masuk' && !canAbsenMasuk.value) return
    if (type === 'pulang' && !canAbsenPulang.value) return

    if (!isLivenessValid.value) {
        if (livenessPassed.value && !isLivenessValid.value) {
            errorMessage.value =
                'Verifikasi liveness sudah kedaluwarsa. Silakan ulangi.'
            resetLiveness()
            generateChallenges()
        } else {
            errorMessage.value =
                'Silakan selesaikan semua challenge liveness terlebih dahulu.'
        }
        return
    }

    if (!locationReady.value) {
        errorMessage.value = 'Lokasi belum berhasil didapatkan.'
        getLocation()
        return
    }

    if (!insideRadius.value) {
        errorMessage.value = `Anda berada di luar radius lokasi absensi. Jarak Anda ${formatDistance(
            distance.value
        )}.`
        return
    }

    if (!cameraReady.value) {
        errorMessage.value = 'Kamera belum siap.'
        return
    }

    const imageData = captureImage()

    if (!imageData) {
        errorMessage.value = 'Gagal mengambil gambar wajah.'
        return
    }

    processing.value = true
    processingType.value = type

    try {
        const imageBlob = dataUrlToBlob(imageData)

        const formData = new FormData()
        formData.append('image', imageBlob, 'face.jpg')
        formData.append('latitude', String(latitude.value))
        formData.append('longitude', String(longitude.value))

        if (gpsAccuracy.value !== null) {
            formData.append('accuracy', String(gpsAccuracy.value))
        }

        formData.append(
            'lokasi_absensi_id',
            String(selectedLokasi.value.id)
        )

        // Kirim data liveness untuk verifikasi server
        formData.append(
            'liveness_challenges',
            JSON.stringify(activeChallenges.value)
        )
        formData.append(
            'liveness_completed_at',
            String(livenessPassedAt.value)
        )

        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute('content')

        const response = await fetch(
            route(
                type === 'masuk'
                    ? 'absensi-staff.masuk'
                    : 'absensi-staff.pulang'
            ),
            {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    ...(csrfToken
                        ? { 'X-CSRF-TOKEN': csrfToken }
                        : {}),
                },
                body: formData,
                credentials: 'same-origin',
            }
        )

        let data = null
        try {
            data = await response.json()
        } catch {
            data = null
        }

        if (!response.ok) {
            throw new Error(
                data?.message || `Absensi ${type} gagal.`
            )
        }

        message.value =
            data?.message || `Absensi ${type} berhasil.`

        resetLiveness()
        generateChallenges()

        router.reload({
            only: [
                'absensiHariIni',
                'jadwal',
                'libur',
                'alasanLibur',
            ],
            preserveScroll: true,
            onFinish: () => {
                resetLiveness()
                generateChallenges()
            },
        })
    } catch (error) {
        console.error('Attendance error:', error)
        errorMessage.value =
            error?.message || `Absensi ${type} gagal.`
    } finally {
        processing.value = false
        processingType.value = null
    }
}

/*
|--------------------------------------------------------------------------
| UPDATE CLOCK
|--------------------------------------------------------------------------
*/

const updateTime = () => {
    currentTime.value = new Date()
}

/*
|--------------------------------------------------------------------------
| CURRENT TIME TEXT
|--------------------------------------------------------------------------
*/

const currentTimeText = computed(() => {
    return new Intl.DateTimeFormat('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
    }).format(currentTime.value)
})

/*
|--------------------------------------------------------------------------
| MOUNT
|--------------------------------------------------------------------------
*/

onMounted(async () => {
    timerInterval = setInterval(updateTime, 1000)

    await startCamera()

    if (cameraReady.value) {
        await initializeFaceLandmarker()
    }

    getLocation()
})

/*
|--------------------------------------------------------------------------
| UNMOUNT
|--------------------------------------------------------------------------
*/

onBeforeUnmount(() => {
    stopFaceDetection()
    stopCamera()

    if (timerInterval) {
        clearInterval(timerInterval)
        timerInterval = null
    }

    if (faceLandmarker) {
        try {
            faceLandmarker.close?.()
        } catch (error) {
            console.error('MediaPipe close error:', error)
        }

        faceLandmarker = null
    }

    landmarkerReady.value = false
})
</script>

<template>
    <Head title="Absensi Staff" />

    <div class="space-y-6">
        <!-- HEADER -->
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">
                Absensi Staff
            </h1>

            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Absensi menggunakan pengenalan wajah, liveness dan lokasi.
            </p>
        </div>

        <!-- HARI LIBUR -->
        <div
            v-if="props.libur"
            class="rounded-2xl border border-amber-200 bg-amber-50 p-5 dark:border-amber-900/50 dark:bg-amber-950/20"
        >
            <div class="flex items-start gap-4">
                <div
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-xl dark:bg-amber-900/40"
                >
                    📅
                </div>

                <div>
                    <h2 class="font-bold text-amber-900 dark:text-amber-300">
                        Hari Libur
                    </h2>

                    <p class="mt-1 text-sm text-amber-700 dark:text-amber-400">
                        {{
                            props.alasanLibur ||
                            'Absensi tidak tersedia hari ini.'
                        }}
                    </p>
                </div>
            </div>
        </div>

        <!-- SUCCESS -->
        <div
            v-if="message"
            class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/20 dark:text-emerald-400"
        >
            ✓ {{ message }}
        </div>

        <!-- ERROR -->
        <div
            v-if="errorMessage"
            class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700 dark:border-red-900/50 dark:bg-red-950/20 dark:text-red-400"
        >
            {{ errorMessage }}
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- CAMERA -->
            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2 dark:border-slate-700 dark:bg-slate-900"
            >
                <!-- CAMERA HEADER -->
                <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-700">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="font-bold text-slate-800 dark:text-white">
                                Verifikasi Wajah
                            </h2>

                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                Selesaikan challenge liveness untuk verifikasi.
                            </p>
                        </div>

                        <!-- LIVENESS BADGE -->
                        <div
                            class="shrink-0 rounded-full px-3 py-1 text-xs font-semibold"
                            :class="
                                isLivenessValid
                                    ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                                    : faceDetected
                                      ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'
                                      : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'
                            "
                        >
                            {{
                                isLivenessValid
                                    ? 'Liveness ✓'
                                    : livenessPassed
                                      ? 'Kedaluwarsa'
                                      : faceDetected
                                        ? 'Challenge Aktif'
                                        : 'Wajah Tidak Terdeteksi'
                            }}
                        </div>
                    </div>
                </div>

                <!-- VIDEO -->
                <div class="relative aspect-video overflow-hidden bg-slate-950">
                    <video
                        ref="video"
                        autoplay
                        muted
                        playsinline
                        class="h-full w-full object-cover"
                        style="transform: scaleX(-1)"
                    ></video>

                    <canvas ref="canvas" class="hidden"></canvas>

                    <!-- FACE FRAME -->
                    <div
                        class="pointer-events-none absolute inset-0 flex items-center justify-center"
                    >
                        <div
                            class="h-64 w-52 rounded-[45%] border-2 transition"
                            :class="
                                isLivenessValid
                                    ? 'border-emerald-400 shadow-[0_0_30px_rgba(52,211,153,0.35)]'
                                    : faceDetected
                                      ? 'border-amber-300'
                                      : 'border-white/70'
                            "
                        ></div>
                    </div>

                    <!-- CHALLENGE OVERLAY -->
                    <div
                        v-if="faceDetected && !isLivenessValid && currentChallenge"
                        class="pointer-events-none absolute left-1/2 top-4 -translate-x-1/2"
                    >
                        <div
                            class="rounded-2xl bg-black/70 px-5 py-3 text-center backdrop-blur"
                        >
                            <p class="text-xs font-medium uppercase tracking-wider text-white/60">
                                Challenge
                                {{ currentChallengeIndex + 1 }}/{{
                                    activeChallenges.length
                                }}
                            </p>

                            <p class="mt-1 text-base font-bold text-white">
                                {{ describeChallenge(currentChallenge) }}
                            </p>

                            <!-- HOLD PROGRESS BAR -->
                            <div
                                v-if="challengeHoldFrames > 0"
                                class="mt-2 h-1.5 w-40 overflow-hidden rounded-full bg-white/20"
                            >
                                <div
                                    class="h-full bg-emerald-400 transition-all"
                                    :style="{
                                        width:
                                            (challengeHoldFrames /
                                                CHALLENGE_HOLD_FRAMES) *
                                                100 +
                                            '%',
                                    }"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <!-- CENTER GUIDE -->
                    <div
                        class="pointer-events-none absolute inset-x-0 bottom-5 flex justify-center"
                    >
                        <div
                            class="rounded-full bg-black/50 px-4 py-2 text-xs font-medium text-white backdrop-blur"
                        >
                            {{
                                isLivenessValid
                                    ? '✓ Wajah terverifikasi'
                                    : faceDetected
                                      ? livenessMessage
                                      : 'Posisikan wajah di tengah'
                            }}
                        </div>
                    </div>

                    <!-- CAMERA ERROR -->
                    <div
                        v-if="cameraError"
                        class="absolute inset-0 flex items-center justify-center bg-slate-950/90 p-6 text-center text-sm text-white"
                    >
                        {{ cameraError }}
                    </div>
                </div>

                <!-- LIVENESS STATUS -->
                <div class="border-t border-slate-200 px-5 py-4 dark:border-slate-700">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full"
                            :class="
                                isLivenessValid
                                    ? 'bg-emerald-100 dark:bg-emerald-900/30'
                                    : faceDetected
                                      ? 'bg-amber-100 dark:bg-amber-900/30'
                                      : 'bg-red-100 dark:bg-red-900/30'
                            "
                        >
                            {{
                                isLivenessValid
                                    ? '✓'
                                    : faceDetected
                                      ? '👁️'
                                      : '!'
                            }}
                        </div>

                        <div class="flex-1">
                            <p
                                class="text-sm font-semibold text-slate-700 dark:text-slate-300"
                            >
                                {{ livenessMessage }}
                            </p>

                            <p
                                class="mt-0.5 text-xs text-slate-500 dark:text-slate-400"
                            >
                                {{
                                    isLivenessValid
                                        ? 'Liveness valid. Silakan lanjutkan absensi.'
                                        : 'Selesaikan semua challenge untuk verifikasi.'
                                }}
                            </p>
                        </div>
                    </div>

                    <!-- CHALLENGE PROGRESS DOTS -->
                    <div
                        v-if="activeChallenges.length > 0"
                        class="mt-4 flex items-center justify-center gap-2"
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
                                            i < currentChallengeIndex,
                                        'bg-indigo-500 text-white ring-2 ring-indigo-300':
                                            i === currentChallengeIndex &&
                                            !isLivenessValid,
                                        'bg-slate-200 text-slate-500 dark:bg-slate-700 dark:text-slate-400':
                                            i > currentChallengeIndex,
                                        'bg-emerald-500 text-white':
                                            isLivenessValid,
                                    }"
                                >
                                    <span v-if="i < currentChallengeIndex || isLivenessValid">
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
                                        i < currentChallengeIndex
                                            ? 'bg-emerald-500'
                                            : 'bg-slate-300 dark:bg-slate-700'
                                    "
                                ></div>
                            </div>
                        </template>
                    </div>

                    <!-- DEBUG POSE (Opsional, bisa dihapus) -->
                    <div
                        v-if="faceDetected && !isLivenessValid"
                        class="mt-3 flex items-center justify-center gap-3 text-[10px] text-slate-400 dark:text-slate-500"
                    >
                        <span>Yaw: {{ yaw.toFixed(3) }}</span>
                        <span>Pitch: {{ pitch.toFixed(3) }}</span>
                        <span>
                            Var:
                            {{ getMovementVariance().toFixed(6) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- PANEL -->
            <div class="space-y-6">
                <!-- USER -->
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900"
                >
                    <p
                        class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                    >
                        Staff
                    </p>

                    <h2 class="mt-2 text-lg font-bold text-slate-800 dark:text-white">
                        {{ props.user?.name || '-' }}
                    </h2>

                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        {{ props.user?.username || '-' }}
                    </p>
                </div>

                <!-- JAM KERJA -->
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900"
                >
                    <div class="flex items-center justify-between">
                        <p
                            class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                        >
                            Jadwal Hari Ini
                        </p>

                        <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">
                            {{ currentTimeText }}
                        </span>
                    </div>

                    <div v-if="props.jadwal" class="mt-4 grid grid-cols-2 gap-3">
                        <div class="rounded-xl bg-slate-50 p-3 dark:bg-slate-800">
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Masuk
                            </p>

                            <p class="mt-1 font-bold text-slate-800 dark:text-white">
                                {{ formatJam(jamMasuk) }}
                            </p>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-3 dark:bg-slate-800">
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Pulang
                            </p>

                            <p class="mt-1 font-bold text-slate-800 dark:text-white">
                                {{ formatJam(jamPulang) }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="mt-4 rounded-xl bg-indigo-50 p-3 text-sm text-indigo-700 dark:bg-indigo-900/20 dark:text-indigo-400"
                    >
                        <p class="font-semibold">
                            {{ timeStatus.label }}
                        </p>

                        <p class="mt-1 text-xs">
                            {{ timeStatus.description }}
                        </p>
                    </div>
                </div>

                <!-- GPS -->
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                Lokasi
                            </p>

                            <p
                                class="mt-1 text-sm font-semibold text-slate-800 dark:text-white"
                            >
                                {{
                                    selectedLokasi?.nama ||
                                    'Mencari lokasi...'
                                }}
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="getLocation"
                            class="rounded-lg bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                        >
                            {{ gpsLoading ? 'Mencari...' : 'Refresh' }}
                        </button>
                    </div>

                    <div v-if="selectedLokasi" class="mt-4 space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-500 dark:text-slate-400">
                                Jarak
                            </span>

                            <span
                                class="font-semibold"
                                :class="
                                    insideRadius ? 'text-emerald-600' : 'text-red-600'
                                "
                            >
                                {{ formatDistance(distance) }}
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-slate-500 dark:text-slate-400">
                                Radius
                            </span>

                            <span class="font-semibold text-slate-700 dark:text-slate-300">
                                {{ selectedLokasi.radius }} meter
                            </span>
                        </div>

                        <div
                            class="mt-3 rounded-lg px-3 py-2 text-center font-semibold"
                            :class="
                                insideRadius
                                    ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400'
                                    : 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400'
                            "
                        >
                            {{
                                insideRadius
                                    ? '✓ Anda berada dalam radius'
                                    : '✕ Anda berada di luar radius'
                            }}
                        </div>
                    </div>

                    <p v-if="gpsError" class="mt-3 text-xs text-red-500">
                        {{ gpsError }}
                    </p>
                </div>

                <!-- ABSENSI HARI INI -->
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900"
                >
                    <p
                        class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                    >
                        Absensi Hari Ini
                    </p>

                    <div class="mt-4 grid grid-cols-2 gap-3">
                        <div class="rounded-xl border border-slate-200 p-3 dark:border-slate-700">
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Masuk
                            </p>

                            <p class="mt-1 font-bold text-slate-800 dark:text-white">
                                {{
                                    props.absensiHariIni?.waktu_masuk?.substring(
                                        0,
                                        5
                                    ) || '-'
                                }}
                            </p>
                        </div>

                        <div class="rounded-xl border border-slate-200 p-3 dark:border-slate-700">
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Pulang
                            </p>

                            <p class="mt-1 font-bold text-slate-800 dark:text-white">
                                {{
                                    props.absensiHariIni?.waktu_keluar?.substring(
                                        0,
                                        5
                                    ) || '-'
                                }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- BUTTON ABSENSI -->
                <div class="space-y-3">
                    <!-- MASUK -->
                    <button
                        type="button"
                        @click="submitAttendance('masuk')"
                        :disabled="!canAbsenMasuk"
                        class="w-full rounded-xl px-5 py-3.5 text-sm font-bold text-white transition"
                        :class="
                            canAbsenMasuk
                                ? 'bg-emerald-600 shadow-sm hover:bg-emerald-700'
                                : 'cursor-not-allowed bg-slate-300 dark:bg-slate-700'
                        "
                    >
                        {{
                            processing && processingType === 'masuk'
                                ? 'Memverifikasi...'
                                : sudahMasuk
                                  ? '✓ Sudah Absen Masuk'
                                  : '📷 Absen Masuk'
                        }}
                    </button>

                    <!-- PULANG -->
                    <button
                        type="button"
                        @click="submitAttendance('pulang')"
                        :disabled="!canAbsenPulang"
                        class="w-full rounded-xl px-5 py-3.5 text-sm font-bold text-white transition"
                        :class="
                            canAbsenPulang
                                ? 'bg-indigo-600 shadow-sm hover:bg-indigo-700'
                                : 'cursor-not-allowed bg-slate-300 dark:bg-slate-700'
                        "
                    >
                        {{
                            processing && processingType === 'pulang'
                                ? 'Memverifikasi...'
                                : sudahPulang
                                  ? '✓ Sudah Absen Pulang'
                                  : '📷 Absen Pulang'
                        }}
                    </button>

                    <!-- LIVENESS HINT -->
                    <p
                        v-if="!isLivenessValid && faceDetected"
                        class="text-center text-xs text-amber-600 dark:text-amber-400"
                    >
                        ⚠️ Selesaikan challenge liveness terlebih dahulu
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>