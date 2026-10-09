<script setup>
import { onMounted, onBeforeUnmount, ref, watch } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png?url'
import markerIcon from 'leaflet/dist/images/marker-icon.png?url'
import markerShadow from 'leaflet/dist/images/marker-shadow.png?url'

const props = defineProps({
    latitude: {
        type: [Number, String],
        default: null,
    },
    longitude: {
        type: [Number, String],
        default: null,
    },
    radius: {
        type: [Number, String],
        default: 100,
    },
})

const emit = defineEmits([
    'update:latitude',
    'update:longitude',
])

const mapElement = ref(null)

let map = null
let marker = null
let radiusCircle = null

/*
|--------------------------------------------------------------------------
| Fix default Leaflet marker icon
|--------------------------------------------------------------------------
*/

delete L.Icon.Default.prototype._getIconUrl

L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
})

/*
|--------------------------------------------------------------------------
| Default location
|--------------------------------------------------------------------------
|
| Digunakan hanya jika koordinat belum tersedia dan GPS tidak digunakan.
| Indonesia sebagai titik awal agar tidak menyesatkan ke kota tertentu.
|
*/

const DEFAULT_LOCATION = [-2.5489, 118.0149]

/*
|--------------------------------------------------------------------------
| Ambil koordinat dari props
|--------------------------------------------------------------------------
*/

const getCoordinates = () => {
    const lat = Number(props.latitude)
    const lng = Number(props.longitude)

    if (
        Number.isFinite(lat) &&
        Number.isFinite(lng) &&
        lat >= -90 &&
        lat <= 90 &&
        lng >= -180 &&
        lng <= 180
    ) {
        return [lat, lng]
    }

    return DEFAULT_LOCATION
}

/*
|--------------------------------------------------------------------------
| Update marker
|--------------------------------------------------------------------------
*/

const updateMarker = (lat, lng, moveMap = true) => {
    const position = [Number(lat), Number(lng)]

    if (!marker) {
        marker = L.marker(position, {
            draggable: true,
        }).addTo(map)

        marker.on('dragend', () => {
            const position = marker.getLatLng()

            emit('update:latitude', Number(position.lat.toFixed(7)))
            emit('update:longitude', Number(position.lng.toFixed(7)))

            updateRadiusCircle(
                position.lat,
                position.lng
            )
        })
    } else {
        marker.setLatLng(position)
    }

    if (moveMap) {
        map.setView(position, 17)
    }

    updateRadiusCircle(
        Number(lat),
        Number(lng)
    )
}

/*
|--------------------------------------------------------------------------
| Update radius circle
|--------------------------------------------------------------------------
*/

const updateRadiusCircle = (lat, lng) => {
    const radius = Number(props.radius) || 100

    if (!radiusCircle) {
        radiusCircle = L.circle([lat, lng], {
            radius,
            weight: 2,
            fillOpacity: 0.15,
        }).addTo(map)
    } else {
        radiusCircle.setLatLng([lat, lng])
        radiusCircle.setRadius(radius)
    }
}

/*
|--------------------------------------------------------------------------
| Klik pada peta
|--------------------------------------------------------------------------
*/

const handleMapClick = (event) => {
    const lat = Number(event.latlng.lat.toFixed(7))
    const lng = Number(event.latlng.lng.toFixed(7))

    emit('update:latitude', lat)
    emit('update:longitude', lng)

    updateMarker(lat, lng, false)
}

/*
|--------------------------------------------------------------------------
| Gunakan lokasi GPS
|--------------------------------------------------------------------------
*/

const useCurrentLocation = () => {
    if (!navigator.geolocation) {
        alert('Browser Anda tidak mendukung GPS.')
        return
    }

    navigator.geolocation.getCurrentPosition(
        (position) => {
            const lat = Number(
                position.coords.latitude.toFixed(7)
            )

            const lng = Number(
                position.coords.longitude.toFixed(7)
            )

            emit('update:latitude', lat)
            emit('update:longitude', lng)

            updateMarker(lat, lng, true)
        },
        (error) => {
            let message = 'Tidak dapat mengambil lokasi Anda.'

            if (error.code === 1) {
                message =
                    'Izin lokasi ditolak. Silakan izinkan akses lokasi pada browser.'
            }

            if (error.code === 2) {
                message =
                    'Lokasi tidak tersedia. Pastikan GPS/lokasi perangkat aktif.'
            }

            if (error.code === 3) {
                message =
                    'Waktu mengambil lokasi habis. Silakan coba lagi.'
            }

            alert(message)
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0,
        }
    )
}

/*
|--------------------------------------------------------------------------
| Expose function ke parent
|--------------------------------------------------------------------------
*/

defineExpose({
    useCurrentLocation,
})

/*
|--------------------------------------------------------------------------
| Watch koordinat
|--------------------------------------------------------------------------
*/

watch(
    () => [props.latitude, props.longitude],
    ([lat, lng]) => {
        if (!map) return

        const latitude = Number(lat)
        const longitude = Number(lng)

        if (
            Number.isFinite(latitude) &&
            Number.isFinite(longitude)
        ) {
            updateMarker(
                latitude,
                longitude,
                false
            )
        }
    }
)

/*
|--------------------------------------------------------------------------
| Watch radius
|--------------------------------------------------------------------------
*/

watch(
    () => props.radius,
    () => {
        if (!map || !marker) return

        const position = marker.getLatLng()

        updateRadiusCircle(
            position.lat,
            position.lng
        )
    }
)

/*
|--------------------------------------------------------------------------
| Initialize map
|--------------------------------------------------------------------------
*/

onMounted(() => {
    const [lat, lng] = getCoordinates()

    map = L.map(mapElement.value, {
        zoomControl: true,
    }).setView(
        [lat, lng],
        5
    )

    L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            attribution:
                '&copy; OpenStreetMap contributors',
            maxZoom: 19,
        }
    ).addTo(map)

    map.on('click', handleMapClick)

    /*
    |--------------------------------------------------------------------------
    | Jika sudah ada koordinat
    |--------------------------------------------------------------------------
    */

    if (
        Number.isFinite(Number(props.latitude)) &&
        Number.isFinite(Number(props.longitude))
    ) {
        updateMarker(
            Number(props.latitude),
            Number(props.longitude),
            true
        )
    }

    /*
    |--------------------------------------------------------------------------
    | Perbaiki ukuran map setelah render
    |--------------------------------------------------------------------------
    */

    setTimeout(() => {
        map?.invalidateSize()
    }, 300)
})

/*
|--------------------------------------------------------------------------
| Cleanup
|--------------------------------------------------------------------------
*/

onBeforeUnmount(() => {
    if (map) {
        map.remove()
        map = null
    }

    marker = null
    radiusCircle = null
})
</script>

<template>
    <div class="space-y-3">
        <!-- MAP -->
        <div
            ref="mapElement"
            class="h-[420px] w-full overflow-hidden rounded-xl border border-slate-200 shadow-sm dark:border-slate-700"
        ></div>

        <!-- INFO -->
        <div
            class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800"
        >
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <div>
                    <p
                        class="text-xs font-medium text-slate-500 dark:text-slate-400"
                    >
                        Latitude
                    </p>

                    <p
                        class="mt-1 text-sm font-semibold text-slate-800 dark:text-white"
                    >
                        {{ latitude || '-' }}
                    </p>
                </div>

                <div>
                    <p
                        class="text-xs font-medium text-slate-500 dark:text-slate-400"
                    >
                        Longitude
                    </p>

                    <p
                        class="mt-1 text-sm font-semibold text-slate-800 dark:text-white"
                    >
                        {{ longitude || '-' }}
                    </p>
                </div>

                <div>
                    <p
                        class="text-xs font-medium text-slate-500 dark:text-slate-400"
                    >
                        Radius
                    </p>

                    <p
                        class="mt-1 text-sm font-semibold text-slate-800 dark:text-white"
                    >
                        {{ radius }} meter
                    </p>
                </div>
            </div>
        </div>

        <!-- PETUNJUK -->
        <div
            class="rounded-lg bg-blue-50 px-4 py-3 text-sm text-blue-700 dark:bg-blue-950/30 dark:text-blue-300"
        >
            <div class="flex gap-2">
                <span>📍</span>

                <p>
                    Klik pada peta atau geser marker untuk menentukan
                    lokasi absensi. Lingkaran menunjukkan area radius
                    absensi.
                </p>
            </div>
        </div>
    </div>
</template>