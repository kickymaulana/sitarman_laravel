<script setup lang="ts">
import LoginForm from "@/components/LoginForm.vue"
import { Button } from "@/components/ui/button"
import { useDark, useToggle } from '@vueuse/core'
import { IconSun, IconMoon, IconFlask, IconLeaf } from '@tabler/icons-vue'

const isDark = useDark()
const toggleDark = useToggle(isDark)
</script>

<template>
  <div
    class="relative flex min-h-svh w-full items-center justify-center p-6 md:p-10 transition-all duration-500 overflow-hidden bg-slate-50"
  >
    <!-- Background Image - Lab Terang & Bersih -->
    <div
      class="absolute inset-0 bg-cover bg-center transition-transform duration-[10s] scale-110 animate-pulse-slow"
      style="background-image: url('https://images.unsplash.com/photo-1581093588401-fbb62a02f120?q=80&w=2070&auto=format&fit=crop');"
    ></div>

    <!-- Overlay Gradien Hijau/Emerald yang Cerah -->
    <div class="absolute inset-0 bg-gradient-to-tr from-emerald-600/40 via-white/10 to-teal-500/30 backdrop-blur-[1px]"></div>

    <!-- Theme Toggle -->
    <div class="absolute right-4 top-4 md:right-10 md:top-10 z-20">
      <Button variant="outline" size="icon" @click="toggleDark()" class="bg-white/80 border-emerald-200 text-emerald-700 hover:bg-emerald-50 backdrop-blur-md shadow-sm">
        <IconSun v-if="isDark" class="size-5 text-amber-500" />
        <IconMoon v-else class="size-5 text-emerald-600" />
      </Button>
    </div>

    <!-- Main Content -->
    <div class="relative z-10 w-full max-w-md">
      <div class="flex flex-col items-center mb-6">
        <div class="relative mb-4 group">
          <!-- Glow effect -->
          <div class="absolute inset-0 bg-emerald-400 blur-xl opacity-40 group-hover:opacity-60 transition-opacity"></div>
          <div class="relative p-4 bg-white rounded-2xl shadow-xl border border-emerald-100 transition-transform group-hover:scale-105">
             <IconFlask class="size-10 text-emerald-600" />
          </div>
        </div>

        <h1 class="text-4xl font-extrabold tracking-tight text-slate-800 drop-shadow-sm">
          SITARMAN
        </h1>
        <div class="flex items-center gap-2 mt-1">
          <div class="h-1 w-8 bg-emerald-500 rounded-full"></div>
          <p class="text-[11px] font-bold text-emerald-800 uppercase tracking-[0.2em]">
            Sistem Thermal Akurasi Report Mutu Analisa
          </p>
          <div class="h-1 w-8 bg-emerald-500 rounded-full"></div>
        </div>
      </div>

      <LoginForm class="shadow-2xl shadow-emerald-900/10" />
    </div>

    <!-- Footer -->
    <div class="absolute bottom-6 z-10 bg-white/60 backdrop-blur-sm px-4 py-1 rounded-full border border-white/50">
      <p class="text-[10px] text-emerald-900 font-bold tracking-wider uppercase">
        &copy; 2026 PT MARK DYNAMICS INDONESIA TBK.
      </p>
    </div>
  </div>
</template>

<style scoped>
@keyframes pulse-slow {
  0%, 100% { transform: scale(1.05); }
  50% { transform: scale(1.1); }
}
.animate-pulse-slow {
  animation: pulse-slow 15s infinite ease-in-out;
}
</style>
