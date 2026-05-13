<script setup lang="ts">
import { cn } from "@/lib/utils"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Field, FieldGroup, FieldLabel } from "@/components/ui/field"
import { Input } from "@/components/ui/input"
import { useForm, Link } from '@inertiajs/vue3'
import {
  IconUser,
  IconUserPlus,
  IconShieldCheck,
  IconLock,
  IconLoader2,
  IconArrowRight
} from '@tabler/icons-vue'

const props = defineProps<{
  class?: string
  roles: Array<{ id: number, name: string }>
}>()

const form = useForm({
  name: '',
  username: '',
  role: '',
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.post(route('register.store'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  })
}
</script>

<template>
  <div :class="cn('flex flex-col gap-6', props.class)">
    <Card class="bg-white/95 dark:bg-slate-900/95 border-none shadow-2xl overflow-hidden ring-1 ring-emerald-100">

      <!-- Top Decorative Bar -->
      <div class="flex h-1.5 w-full">
        <div class="flex-1 bg-emerald-500"></div>
        <div class="flex-1 bg-amber-400"></div>
        <div class="flex-1 bg-teal-500"></div>
      </div>

      <CardHeader class="pt-8 pb-4">
        <CardTitle class="text-2xl font-black text-slate-800 flex items-center gap-2">
          <IconUserPlus class="size-6 text-emerald-600" />
          Registrasi Akun Baru
        </CardTitle>
        <CardDescription class="font-medium text-slate-500 italic">
          Lengkapi data personil untuk bergabung dalam sistem mutu analisa.
        </CardDescription>
      </CardHeader>

      <CardContent>
        <form @submit.prevent="submit">
          <FieldGroup class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">

            <!-- Nama Lengkap (Full Width) -->
            <Field class="md:col-span-2">
              <FieldLabel class="text-[10px] font-black text-emerald-700 uppercase tracking-widest ml-1">Nama Lengkap</FieldLabel>
              <div class="relative group">
                <IconUser class="absolute left-3 top-3 size-4 text-emerald-300 group-focus-within:text-emerald-600 transition-colors" />
                <Input
                  id="name"
                  v-model="form.name"
                  type="text"
                  placeholder="Contoh: Kicky Maulana"
                  class="pl-10 h-11 border-slate-200 focus-visible:ring-emerald-500 bg-slate-50/50"
                  required
                />
              </div>
              <p v-if="form.errors.name" class="text-destructive text-[10px] font-bold mt-1 ml-1 lowercase italic">* {{ form.errors.name }}</p>
            </Field>

            <!-- Username & Role -->
            <Field>
              <FieldLabel class="text-[10px] font-black text-emerald-700 uppercase tracking-widest ml-1">Username</FieldLabel>
              <div class="relative group">
                <IconUser class="absolute left-3 top-3 size-4 text-emerald-300 group-focus-within:text-emerald-600 transition-colors" />
                <Input
                  id="username"
                  v-model="form.username"
                  type="text"
                  placeholder="ID Karyawan"
                  class="pl-10 h-11 border-slate-200 focus-visible:ring-emerald-500 bg-slate-50/50"
                  required
                />
              </div>
              <p v-if="form.errors.username" class="text-destructive text-[10px] font-bold mt-1 ml-1 lowercase italic">* {{ form.errors.username }}</p>
            </Field>

            <Field>
              <FieldLabel class="text-[10px] font-black text-emerald-700 uppercase tracking-widest ml-1">Jabatan / Role</FieldLabel>
              <div class="relative group">
                <IconShieldCheck class="absolute left-3 top-3 size-4 text-emerald-300 group-focus-within:text-emerald-600 z-10 transition-colors" />
                <select
                  id="role"
                  v-model="form.role"
                  class="flex h-11 w-full rounded-md border border-slate-200 bg-slate-50/50 pl-10 pr-10 py-2 text-sm focus-visible:ring-2 focus-visible:ring-emerald-500 appearance-none shadow-sm font-medium"
                  required
                >
                  <option value="" disabled>Pilih Role</option>
                  <option v-for="role in roles" :key="role.id" :value="role.name">
                    {{ role.name.toUpperCase() }}
                  </option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-emerald-300">
                    <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" /></svg>
                </div>
              </div>
              <p v-if="form.errors.role" class="text-destructive text-[10px] font-bold mt-1 ml-1 lowercase italic">* {{ form.errors.role }}</p>
            </Field>

            <!-- Password Fields -->
            <Field>
              <FieldLabel class="text-[10px] font-black text-emerald-700 uppercase tracking-widest ml-1">Password</FieldLabel>
              <div class="relative group">
                <IconLock class="absolute left-3 top-3 size-4 text-emerald-300 group-focus-within:text-emerald-600 transition-colors" />
                <Input
                  id="password"
                  v-model="form.password"
                  type="password"
                  placeholder="••••••••"
                  class="pl-10 h-11 border-slate-200 focus-visible:ring-emerald-500 bg-slate-50/50"
                  required
                />
              </div>
            </Field>

            <Field>
              <FieldLabel class="text-[10px] font-black text-emerald-700 uppercase tracking-widest ml-1">Konfirmasi</FieldLabel>
              <div class="relative group">
                <IconLock class="absolute left-3 top-3 size-4 text-emerald-300 group-focus-within:text-emerald-600 transition-colors" />
                <Input
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  type="password"
                  placeholder="••••••••"
                  class="pl-10 h-11 border-slate-200 focus-visible:ring-emerald-500 bg-slate-50/50"
                  required
                />
              </div>
              <p v-if="form.errors.password" class="text-destructive text-[10px] font-bold mt-1 ml-1 lowercase italic">* {{ form.errors.password }}</p>
            </Field>

            <!-- Submit Button (Full Width) -->
            <Field class="md:col-span-2 mt-4">
              <Button
                type="submit"
                :disabled="form.processing"
                class="w-full h-12 bg-emerald-600 hover:bg-emerald-700 text-white font-black shadow-lg shadow-emerald-200 transition-all active:scale-[0.98] rounded-xl"
              >
                <IconLoader2 v-if="form.processing" class="mr-2 size-5 animate-spin" />
                <span v-else class="flex items-center gap-2">
                  DAFTARKAN AKUN <IconArrowRight class="size-4" />
                </span>
              </Button>
            </Field>
          </FieldGroup>
        </form>

        <div class="mt-8 text-center bg-slate-50 py-4 -mx-6 -mb-6 border-t border-emerald-50 dark:bg-slate-950/50">
          <p class="text-xs font-bold text-slate-500 uppercase tracking-tighter">
            Sudah memiliki kredensial?
            <Link :href="route('login')" class="text-emerald-600 hover:text-emerald-700 underline underline-offset-4 ml-1">
              Masuk ke Sistem
            </Link>
          </p>
        </div>
      </CardContent>
    </Card>
  </div>
</template>
