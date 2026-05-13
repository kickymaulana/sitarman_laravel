<script setup lang="ts">
import { cn } from "@/lib/utils"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Field, FieldGroup, FieldLabel } from "@/components/ui/field"
import { Input } from "@/components/ui/input"
import { useForm } from '@inertiajs/vue3'
import { IconLock, IconUser, IconReportAnalytics } from '@tabler/icons-vue'

const props = defineProps<{ class?: string }>()

const form = useForm({
  username: '',
  password: '',
})

const submit = () => {
  form.post(route('login.store'), {
    onFinish: () => form.reset('password'),
  })
}
</script>

<template>
  <div :class="cn('flex flex-col gap-6', props.class)">
    <Card class="bg-white/95 dark:bg-slate-900/95 border-none shadow-xl overflow-hidden ring-1 ring-emerald-100">

      <!-- Top Decorative Bar (Cerah) -->
      <div class="flex h-1.5 w-full">
        <div class="flex-1 bg-emerald-400"></div>
        <div class="flex-1 bg-amber-400"></div>
        <div class="flex-1 bg-teal-400"></div>
      </div>

      <CardHeader class="pb-4 pt-6 text-center md:text-left">
        <CardTitle class="text-2xl font-black text-slate-800 dark:text-white flex items-center justify-center md:justify-start gap-2">
          <IconReportAnalytics class="size-6 text-emerald-600" />
          Selamat Datang
        </CardTitle>
        <CardDescription class="text-slate-500 font-medium">
          Masuk untuk akses ke Sistem
        </CardDescription>
      </CardHeader>

      <CardContent>
        <form @submit.prevent="submit" class="space-y-4">
          <FieldGroup class="gap-4">
            <Field>
              <FieldLabel class="text-[10px] font-black text-emerald-700 uppercase tracking-widest ml-1">Username</FieldLabel>
              <div class="relative group">
                <IconUser class="absolute left-3 top-3 size-4 text-emerald-300 group-focus-within:text-emerald-600 transition-colors" />
                <Input
                  v-model="form.username"
                  type="text"
                  placeholder="Username Anda"
                  class="pl-10 h-12 border-slate-200 focus-visible:ring-emerald-500 bg-slate-50/50"
                  required
                />
              </div>
            </Field>

            <Field>
              <div class="flex items-center justify-between ml-1">
                <FieldLabel class="text-[10px] font-black text-emerald-700 uppercase tracking-widest">Password</FieldLabel>
                <a href="#" class="text-[10px] font-bold text-amber-600 hover:text-amber-700 uppercase">Lupa?</a>
              </div>
              <div class="relative group">
                <IconLock class="absolute left-3 top-3 size-4 text-emerald-300 group-focus-within:text-emerald-600 transition-colors" />
                <Input
                  type="password"
                  v-model="form.password"
                  class="pl-10 h-12 border-slate-200 focus-visible:ring-emerald-500 bg-slate-50/50"
                  placeholder="••••••••"
                  required
                />
              </div>
            </Field>

            <Button
              type="submit"
              :disabled="form.processing"
              class="w-full h-12 bg-emerald-600 hover:bg-emerald-700 text-white font-black shadow-lg shadow-emerald-200 transition-all active:scale-[0.98] mt-2 rounded-xl"
            >
              {{ form.processing ? 'LOADING...' : 'SIGN IN' }}
            </Button>
          </FieldGroup>
        </form>
      </CardContent>
    </Card>

    <!-- Support Info -->
    <div class="text-center">
      <p class="text-[10px] text-emerald-900/60 font-bold">
        Butuh bantuan akses? Hubungi <span class="text-emerald-700 underline font-black text-xs">IT Department</span>
      </p>
    </div>
  </div>
</template>
