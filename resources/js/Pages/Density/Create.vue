<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { IconArrowLeft, IconDeviceFloppy, IconLoader2 } from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

// Definisikan props untuk menerima data user dari controller
defineProps<{
    users: Array<{ id: number; name: string }>;
}>();

// Inisialisasi semua field sesuai struktur data baru
const form = useForm({
    tgl: "",
    density_user_id: "",
    water_absoription_user_id: "",
    spec: "-",
    mulai_proses: "00:00:00",
    selesai_proses: "00:00:00",
    temp_air: 0,
});
</script>

<template>
    <Head title="Tambah Data Baru" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child class="rounded-full">
                <Link :href="route('density.index')">
                    <IconArrowLeft class="size-4" />
                </Link>
            </Button>
            <h2 class="text-3xl font-bold tracking-tight">Tambah Data</h2>
        </div>

        <div class="max-w-3xl">
            <Card class="border-none shadow-lg">
                <CardHeader>
                    <CardTitle class="text-primary">Master Data Density & Water Absorption</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="form.post(route('density.store'))" class="space-y-6">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Input: Tanggal -->
                            <div class="grid gap-2">
                                <Label for="tgl">Tanggal</Label>
                                <Input type="date" id="tgl" v-model="form.tgl" />
                                <p v-if="form.errors.tgl" class="text-sm text-destructive">{{ form.errors.tgl }}</p>
                            </div>

                            <!-- Input: Spec -->
                            <div class="grid gap-2">
                                <Label for="spec">Spec</Label>
                                <Input type="text" id="spec" v-model="form.spec" placeholder="Masukkan spesifikasi..." />
                                <p v-if="form.errors.spec" class="text-sm text-destructive">{{ form.errors.spec }}</p>
                            </div>

                            <!-- Select: Operator Density -->
                            <div class="grid gap-2">
                                <Label for="density_user_id">Operator Density</Label>
                                <select id="density_user_id" v-model="form.density_user_id" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                                    <option value="" disabled>-- Pilih Operator Density --</option>
                                    <option v-for="user in users" :key="user.id" :value="user.id">
                                        {{ user.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.density_user_id" class="text-sm text-destructive">{{ form.errors.density_user_id }}</p>
                            </div>

                            <!-- Select: Operator Water Absorption -->
                            <div class="grid gap-2">
                                <Label for="water_absoription_user_id">Operator Water Absorption</Label>
                                <select id="water_absoription_user_id" v-model="form.water_absoription_user_id" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                                    <option value="" disabled>-- Pilih Operator Water Absorption --</option>
                                    <option v-for="user in users" :key="user.id" :value="user.id">
                                        {{ user.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.water_absoription_user_id" class="text-sm text-destructive">{{ form.errors.water_absoription_user_id }}</p>
                            </div>

                            <!-- Input: Mulai Proses -->
                            <div class="grid gap-2">
                                <Label for="mulai_proses">Mulai Proses (Format H:i:s)</Label>
                                <Input type="text" id="mulai_proses" v-model="form.mulai_proses" placeholder="08:00:00" />
                                <p v-if="form.errors.mulai_proses" class="text-sm text-destructive">{{ form.errors.mulai_proses }}</p>
                            </div>

                            <!-- Input: Selesai Proses -->
                            <div class="grid gap-2">
                                <Label for="selesai_proses">Selesai Proses (Format H:i:s)</Label>
                                <Input type="text" id="selesai_proses" v-model="form.selesai_proses" placeholder="10:00:00" />
                                <p v-if="form.errors.selesai_proses" class="text-sm text-destructive">{{ form.errors.selesai_proses }}</p>
                            </div>

                            <!-- Input: Temperatur Air -->
                            <div class="grid gap-2 md:col-span-2">
                                <Label for="temp_air">Temperatur Air (°C)</Label>
                                <Input type="number" id="temp_air" v-model.number="form.temp_air" />
                                <p v-if="form.errors.temp_air" class="text-sm text-destructive">{{ form.errors.temp_air }}</p>
                            </div>
                        </div>

                        <Button type="submit" :disabled="form.processing" class="w-full bg-primary hover:bg-primary/90 mt-4 shadow-md">
                            <IconLoader2 v-if="form.processing" class="mr-2 animate-spin" />
                            <IconDeviceFloppy v-else class="mr-2" /> Simpan Data Baru
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
