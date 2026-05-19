<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from "@/components/ui/alert-dialog";
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu";
import { IconArrowLeft, IconEye, IconTrash, IconDotsVertical, IconFlame, IconClock, IconThermometer, IconPencil } from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    thermalshock: {
        id: number;
        thermal_oven_id: number;
        thermal_pintu_id: number;
        hari_tgl: string;
        suhu_testing: number;
        suhu_motor: string | null;
        suhu_display: number;
        suhu_actual: number;
        jam_awal_proses: string;
        jam_capai_suhu: string;
        suhu_awal: number;
        suhu_air: string;
        jam_mulai_tembak: string;
        jam_selesai_tembak: string;
        thermal_oven: { thermal_oven: string } | null;
        thermal_pintu: { thermal_pintu: string } | null;
    };
}>();

// Formatter Jam agar bersih dari detik (HH:mm:ss -> HH:mm)
const formatTime = (timeString: string | undefined) => {
    if (!timeString) return "-";
    return timeString.substring(0, 5);
};
</script>

<template>
    <Head title="Detail Thermal Shock" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">

        <!-- Header Aksi -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child class="rounded-full">
                    <Link :href="route('thermalshock.index')">
                        <IconArrowLeft class="size-4" />
                    </Link>
                </Button>
                <h2 class="text-3xl font-bold tracking-tight">Detail Thermal Shock</h2>
            </div>

            <div class="flex items-center gap-2">
                <!-- Tombol Edit Utama -->
                <Button as-child variant="outline" size="sm" class="shadow-sm">
                    <Link :href="route('produk.index', props.thermalshock.id)">
                        <IconEye class="mr-2 size-4 text-primary" /> Produk
                    </Link>
                </Button>

                <!-- Dropdown untuk Aksi Kritikal (Hapus) -->
                <AlertDialog>
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button variant="ghost" size="icon"><IconDotsVertical class="size-4" /></Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <AlertDialogTrigger as-child>
                                <DropdownMenuItem class="text-destructive cursor-pointer">
                                    <IconTrash class="mr-2 size-4" />Hapus
                                </DropdownMenuItem>
                                <DropdownMenuItem class="cursor-pointer" as-child>
                                    <Link :href="route('thermalshock.edit', props.thermalshock.id)">
                                        <IconPencil class="mr-2 size-4 text-muted-foreground" />
                                        Edit
                                    </Link>
                                </DropdownMenuItem>
                            </AlertDialogTrigger>
                        </DropdownMenuContent>
                    </DropdownMenu>
                    <AlertDialogContent>
                        <AlertDialogHeader>
                            <AlertDialogTitle>Hapus Data?</AlertDialogTitle>
                            <AlertDialogDescription>
                                Apakah Anda yakin ingin menghapus permanen log thermal shock tanggal
                                <strong class="text-foreground">
                                    {{ new Date(props.thermalshock.hari_tgl).toLocaleDateString("id-ID", { day: "2-digit", month: "long", year: "numeric" }) }}
                                </strong>?
                            </AlertDialogDescription>
                        </AlertDialogHeader>
                        <AlertDialogFooter>
                            <AlertDialogCancel>Batal</AlertDialogCancel>
                            <AlertDialogAction @click="router.delete(route('thermalshock.destroy', props.thermalshock.id))" class="bg-destructive text-white hover:bg-destructive/90">
                                Ya, Hapus
                            </AlertDialogAction>
                        </AlertDialogFooter>
                    </AlertDialogContent>
                </AlertDialog>
            </div>
        </div>

        <!-- Konten Utama -->
        <div class="max-w-4xl grid grid-cols-1 gap-6">
            <Card class="border-none shadow-lg">
                <CardHeader class="border-b bg-muted/20 pb-4">
                    <div class="flex items-center gap-2">
                        <IconFlame class="size-5 text-primary" />
                        <CardTitle class="text-lg">Informasi Umum & Relasi</CardTitle>
                    </div>
                </CardHeader>
                <CardContent class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div>
                        <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Hari / Tanggal</span>
                        <p class="text-base font-medium mt-1">
                            {{ new Date(props.thermalshock.hari_tgl).toLocaleDateString("id-ID", { weekday: "long", day: "2-digit", month: "long", year: "numeric" }) }}
                        </p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Thermal Oven</span>
                        <p class="text-base font-bold text-primary mt-1">
                            {{ props.thermalshock.thermal_oven?.thermal_oven ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Thermal Pintu</span>
                        <p class="text-base font-medium mt-1">
                            {{ props.thermalshock.thermal_pintu?.thermal_pintu ?? '-' }}
                        </p>
                    </div>
                </CardContent>
            </Card>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Card Parameter Suhu -->
                <Card class="border-none shadow-lg">
                    <CardHeader class="border-b bg-muted/20 pb-4">
                        <div class="flex items-center gap-2">
                            <IconThermometer class="size-5 text-orange-500" />
                            <CardTitle class="text-lg">Parameter Suhu</CardTitle>
                        </div>
                    </CardHeader>
                    <CardContent class="p-6 grid grid-cols-2 gap-4">
                        <div class="border-b pb-2">
                            <span class="text-xs font-medium text-muted-foreground">Suhu Testing</span>
                            <p class="text-lg font-semibold">{{ props.thermalshock.suhu_testing }}°C</p>
                        </div>
                        <div class="border-b pb-2">
                            <span class="text-xs font-medium text-muted-foreground">Suhu Awal</span>
                            <p class="text-lg font-semibold">{{ props.thermalshock.suhu_awal }}°C</p>
                        </div>
                        <div class="border-b pb-2">
                            <span class="text-xs font-medium text-muted-foreground">Suhu Display</span>
                            <p class="text-lg font-semibold">{{ props.thermalshock.suhu_display }}°C</p>
                        </div>
                        <div class="border-b pb-2">
                            <span class="text-xs font-medium text-muted-foreground">Suhu Actual</span>
                            <p class="text-lg font-semibold text-primary">{{ props.thermalshock.suhu_actual }}°C</p>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-muted-foreground">Suhu Motor</span>
                            <p class="text-base font-medium">{{ props.thermalshock.suhu_motor ?? '-' }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-muted-foreground">Suhu Air</span>
                            <p class="text-base font-medium">{{ props.thermalshock.suhu_air }}</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Card Waktu Proses -->
                <Card class="border-none shadow-lg">
                    <CardHeader class="border-b bg-muted/20 pb-4">
                        <div class="flex items-center gap-2">
                            <IconClock class="size-5 text-blue-500" />
                            <CardTitle class="text-lg">Alur & Waktu Proses</CardTitle>
                        </div>
                    </CardHeader>
                    <CardContent class="p-6 space-y-4">
                        <div class="flex justify-between items-center border-b pb-2">
                            <span class="text-sm font-medium text-muted-foreground">Jam Awal Proses</span>
                            <span class="text-base font-semibold bg-secondary px-2.5 py-0.5 rounded text-secondary-foreground">
                                {{ formatTime(props.thermalshock.jam_awal_proses) }} WIB
                            </span>
                        </div>
                        <div class="flex justify-between items-center border-b pb-2">
                            <span class="text-sm font-medium text-muted-foreground">Jam Capai Suhu</span>
                            <span class="text-base font-semibold bg-secondary px-2.5 py-0.5 rounded text-secondary-foreground">
                                {{ formatTime(props.thermalshock.jam_capai_suhu) }} WIB
                            </span>
                        </div>
                        <div class="flex justify-between items-center border-b pb-2">
                            <span class="text-sm font-medium text-muted-foreground">Jam Mulai Tembak</span>
                            <span class="text-base font-semibold bg-primary/10 text-primary px-2.5 py-0.5 rounded">
                                {{ formatTime(props.thermalshock.jam_mulai_tembak) }} WIB
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-muted-foreground">Jam Selesai Tembak</span>
                            <span class="text-base font-semibold bg-destructive/10 text-destructive px-2.5 py-0.5 rounded">
                                {{ formatTime(props.thermalshock.jam_selesai_tembak) }} WIB
                            </span>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>

    </div>
</template>
