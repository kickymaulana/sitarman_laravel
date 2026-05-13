<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head, Link, router } from "@inertiajs/vue3"
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import {
  IconSearch,
  IconX,
} from "@tabler/icons-vue"
import { ref, watch } from 'vue'

// Persistent Layout
defineOptions({ layout: AuthenticatedLayout })

// Definisi Props (Disesuaikan dengan field baru)
const props = defineProps<{
  users: {
    data: Array<{
      id: number
      name: string
      username: string
      whatsapp: string
      roles: Array<{
        id: number
        name: string
      }>
    }>
    links: Array<{
      url: string | null
      label: string
      active: boolean
    }>
    from: number
    to: number
    total: number
  },
  filters: {
    search: string
  }
}>()

// Logika Search
const search = ref(props.filters.search || '')

let timeout: ReturnType<typeof setTimeout>
watch(search, (value) => {
  clearTimeout(timeout)
  timeout = setTimeout(() => {
    router.get(
      route('daftar.pengguna.index'), // Sesuaikan dengan nama route Anda
      { search: value },
      { preserveState: true, replace: true }
    )
  }, 500)
})

const clearSearch = () => {
  search.value = ''
}

const cleanLabel = (label: string) => {
  if (label.includes('Previous')) return 'Sebelumnya'
  if (label.includes('Next')) return 'Selanjutnya'
  return label
}
</script>

<template>
  <Head title="Daftar Pengguna" />

  <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">
    <Card class="border-none shadow-sm">
      <CardHeader class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6">
        <CardTitle class="text-xl font-bold italic">Daftar Pengguna</CardTitle>

        <div class="flex items-center gap-2 w-full md:w-auto">
          <div class="relative w-full md:w-72">
            <IconSearch class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
            <Input
              v-model="search"
              placeholder="Cari nama, username, atau wa..."
              class="pl-10 pr-10 h-10 shadow-sm focus-visible:ring-primary"
            />
            <button
              v-if="search"
              @click="clearSearch"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
            >
              <IconX class="size-4" />
            </button>
          </div>
        </div>
      </CardHeader>

      <CardContent>
        <div class="rounded-md border bg-card">
          <Table>
            <TableHeader>
              <TableRow class="bg-muted/50">
                <TableHead class="w-[200px] uppercase text-[10px] font-bold">Username</TableHead>
                <TableHead class="uppercase text-[10px] font-bold">Nama Lengkap</TableHead>
                <TableHead class="uppercase text-[10px] font-bold">Role / Peran</TableHead>
                <TableHead class="uppercase text-[10px] font-bold">WhatsApp</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-if="users.data.length === 0">
                <TableCell colspan="4" class="h-24 text-center text-muted-foreground">
                  Data tidak ditemukan.
                </TableCell>
              </TableRow>

              <TableRow v-for="user in users.data" :key="user.id" class="hover:bg-muted/30 transition-colors">
                <TableCell>
                   <span class="text-xs font-mono bg-muted px-2 py-0.5 rounded border">{{ user.username }}</span>
                </TableCell>
                <TableCell class="font-medium text-sm">{{ user.name }}</TableCell>
                <TableCell>
                    <div class="flex flex-wrap gap-1">
                        <span
                          v-for="role in user.roles"
                          :key="role.id"
                          class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-primary/10 text-primary uppercase tracking-tight"
                        >
                          {{ role.name }}
                        </span>
                        <span v-if="user.roles.length === 0" class="text-[10px] text-muted-foreground italic">
                          No Role
                        </span>
                    </div>
                </TableCell>
                <TableCell class="text-sm font-mono text-muted-foreground">
                  {{ user.whatsapp }}
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>

        <div class="flex flex-col md:flex-row items-center justify-between gap-4 mt-6">
          <p class="text-[11px] text-muted-foreground italic">
            Menampilkan {{ users.from ?? 0 }} - {{ users.to ?? 0 }} dari {{ users.total }} pengguna
          </p>

          <nav class="flex items-center gap-1">
            <template v-for="(link, k) in users.links" :key="k">
              <Button
                v-if="link.url === null"
                variant="outline"
                size="sm"
                disabled
                class="opacity-50 text-xs px-3 h-8"
                v-html="cleanLabel(link.label)"
              />
              <Button
                v-else
                as-child
                variant="outline"
                size="sm"
                class="text-xs px-3 h-8 shadow-sm"
                :class="{ 'bg-primary text-primary-foreground hover:bg-primary/90 hover:text-primary-foreground': link.active }"
              >
                <Link :href="link.url" v-html="cleanLabel(link.label)" />
              </Button>
            </template>
          </nav>
        </div>
      </CardContent>
    </Card>
  </div>
</template>
