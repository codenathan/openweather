<script lang="ts" setup>
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import { search } from '@/routes/weather';

const props = defineProps({
    weather: Object,
    city: String,
})

const form = useForm({
    city: props.city || '',
})

function submit() {
    form.post(search.url())
}

const iconUrl = computed(() => {
    const icon = props.weather?.weather?.[0]?.icon
    return icon ? `https://openweathermap.org/img/wn/${icon}@2x.png` : null
})

const temperature = computed(() => {
    const temp = props.weather?.main?.temp
    return typeof temp === 'number' ? `${Math.round(temp)}°C` : ''
})

const feelsLike = computed(() => {
    const feel = props.weather?.main?.feels_like
    return typeof feel === 'number' ? `${Math.round(feel)}°C` : ''
})

const windDirection = computed(() => {
    const deg = props.weather?.wind?.deg
    return getWindDirection(deg)
})

function getWindDirection(deg: number | undefined) {
    if (deg === undefined || deg === null) return ''
    const directions = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW']
    const index = Math.round(deg / 45) % 8
    return directions[index]
}
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-sky-100 to-blue-200 flex flex-col items-center justify-center p-6">
        <div class="w-full max-w-md bg-white rounded-2xl shadow p-6">
            <h1 class="text-2xl font-bold mb-4 text-center text-blue-700">
                UK Weather Lookup
            </h1>

            <form @submit.prevent="submit" class="flex gap-2 mb-6">
                <input
                    v-model="form.city"
                    type="text"
                    placeholder="Enter city (e.g. London)"
                    class="flex-1 border border-gray-300 rounded-lg px-3 py-2"
                    required
                />
                <button
                    type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
                >
                    Search
                </button>
            </form>

            <div v-if="form.processing" class="text-center text-gray-500">
                Loading weather data...
            </div>

            <!-- Weather Result -->
            <div v-if="props.weather && props.weather.cod === 200 && !form.processing" class="text-center space-y-4">
                <div class="flex flex-col items-center">
                    <h2 class="text-xl font-semibold">{{ props.weather.name }}, {{ props.weather.sys?.country }}</h2>
                    <p class="text-gray-500 text-sm">Lat: {{ props.weather.coord?.lat }}, Lon: {{ props.weather.coord?.lon }}</p>
                </div>

                <div class="flex flex-col items-center space-y-2">
                    <img v-if="iconUrl" :src="iconUrl" alt="Weather icon" class="w-20 h-20" />
                    <p class="capitalize text-lg text-gray-600">
                        {{ props.weather.weather?.[0]?.description }}
                    </p>
                </div>

                <div class="text-4xl font-bold text-blue-800">{{ temperature }}</div>
                <div class="text-gray-600">Feels like: {{ feelsLike }}</div>

                <div class="mt-4 grid grid-cols-2 gap-3 text-sm text-gray-700">
                    <div>Humidity: <span class="font-medium">{{ props.weather.main?.humidity }}%</span></div>
                    <div>
                        Wind:
                        <span class="font-medium">
                            {{ props.weather.wind?.speed }} m/s
                            <span v-if="windDirection">({{ windDirection }})</span>
                          </span>
                    </div>
                    <div>Pressure: <span class="font-medium">{{ props.weather.main?.pressure }} hPa</span></div>
                    <div>Clouds: <span class="font-medium">{{ props.weather.clouds?.all }}%</span></div>
                </div>
            </div>

            <div v-else-if="props.weather && (props.weather.error || props.weather.cod !== 200)" class="text-red-500 text-center mt-4">
                {{ props.weather.message || 'City not found or unable to fetch weather data.' }}
            </div>
        </div>
    </div>
</template>
