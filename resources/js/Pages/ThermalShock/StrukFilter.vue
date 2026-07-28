<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Label } from '@/components/ui/label'
import { IconArrowLeft, IconFlame, IconReceipt } from '@tabler/icons-vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

defineOptions({ layout: AuthenticatedLayout })

const props = defineProps<{ ovens: { id: number; oven: string }[] }>()

const form = useForm({
    tanggal_keluar_oven: '',
    oven_id: '',
    kode_bakar: '',
    sampel: '',
})

const submit = () => {
    form.post(route('thermalshock.strukFilterProcess'))
}
</script>

<template>
    <Head title="Struk Filter - Thermal Shock" />
    <div class="flex flex-col gap-6 p-4 md:p-8">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child class="rounded-full">
                <Link :href="route('thermalshock.index')"><IconArrowLeft class="size-4" /></Link>
            </Button>
            <h2 class="text-xl md:text-2xl font-bold tracking-tight flex items-center gap-2">
                <IconReceipt class="size-6 text-primary" />
                Struk Filter
            </h2>
        </div>

        <div class="max-w-md mx-auto w-full">
            <Card>
                <CardHeader>
                    <CardTitle class="text-sm font-bold flex items-center gap-2">
                        <IconFlame class="size-4 text-amber-500" />
                        Pilih Filter untuk Cetak Struk
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="space-y-1.5">
                            <Label class="text-xs font-semibold">Tanggal Keluar Oven</Label>
                            <Input type="date" v-model="form.tanggal_keluar_oven" class="h-9" />
                        </div>

                        <div class="space-y-1.5">
                            <Label class="text-xs font-semibold">Oven</Label>
                            <Select v-model="form.oven_id">
                                <SelectTrigger class="h-9"><SelectValue placeholder="Pilih Oven" /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="o in ovens" :key="o.id" :value="String(o.id)">{{ o.oven }}</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <div class="space-y-1.5">
                            <Label class="text-xs font-semibold">Kode Bakar</Label>
                            <Input type="number" v-model="form.kode_bakar" placeholder="Contoh: 220" class="h-9" />
                        </div>

                        <div class="space-y-1.5">
                            <Label class="text-xs font-semibold">Sampel</Label>
                            <Input v-model="form.sampel" placeholder="Cari sampel..." class="h-9" />
                        </div>

                        <Button type="submit" class="w-full bg-zinc-800 hover:bg-zinc-900 text-white" :disabled="form.processing">
                            <IconReceipt class="mr-2 size-4" />
                            {{ form.processing ? 'Memproses...' : 'Cetak Struk' }}
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
