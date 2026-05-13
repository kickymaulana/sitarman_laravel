<script setup lang="ts">
import RegisterForm from "@/components/RegisterForm.vue"
import { Button } from "@/components/ui/button"
import { useDark, useToggle } from '@vueuse/core'
import { IconSun, IconMoon, IconFlask } from '@tabler/icons-vue'
import { Head } from '@inertiajs/vue3'

defineProps<{
  roles: Array<{ id: number, name: string }>
}>()

const isDark = useDark()
const toggleDark = useToggle(isDark)
</script>

<template>
  <Head title="Registrasi Personil - SITARMAN" />

  <div
    class="relative flex min-h-svh w-full items-center justify-center p-4 md:p-10 bg-cover bg-center bg-no-repeat transition-all duration-500 overflow-hidden"
    style="background-image: url('https://images.unsplash.com/photo-1581093588401-fbb62a02f120?q=80&w=2070&auto=format&fit=crop');"
  >
    <!-- Overlay Emerald Cerah -->
    <div class="absolute inset-0 bg-gradient-to-tr from-emerald-600/40 via-white/20 to-teal-500/30 backdrop-blur-[2px]"></div>

    <!-- Theme Toggle -->
    <div class="absolute right-4 top-4 md:right-10 md:top-10 z-20">
      <Button variant="outline" size="icon" @click="toggleDark()" class="bg-white/80 border-emerald-200 text-emerald-700 hover:bg-emerald-50 backdrop-blur-md shadow-sm">
        <IconSun v-if="isDark" class="size-5 text-amber-500" />
        <IconMoon v-else class="size-5 text-emerald-600" />
      </Button>
    </div>

    <div class="relative z-10 w-full max-w-2xl">
      <!-- Logo Section -->
      <div class="flex flex-col items-center mb-6">
        <div class="p-3 bg-white rounded-2xl shadow-xl border border-emerald-100 mb-3 group transition-transform hover:scale-110">
           <IconFlask class="size-8 text-emerald-600" />
        </div>
        <h1 class="text-3xl font-black tracking-tighter text-slate-800 drop-shadow-sm">SITARMAN</h1>
        <p class="text-[10px] font-bold text-emerald-800 uppercase tracking-[0.3em]">Registration Portal</p>
      </div>

      <RegisterForm
        :roles="roles"
        class="shadow-2xl border-none"
      />
    </div>

    <!-- Footer -->
    <div class="absolute bottom-4 text-[10px] text-emerald-900 font-bold tracking-widest uppercase z-10 bg-white/40 backdrop-blur-sm px-4 py-1 rounded-full border border-white/50">
      &copy; 2026 PT MARK DYNAMICS INDONESIA TBK.
    </div>
  </div>
</template>
