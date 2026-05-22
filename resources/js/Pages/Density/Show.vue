<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from "@/components/ui/alert-dialog";
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu";
import { IconArrowLeft, IconEye, IconTrash, IconDotsVertical, IconHammer, IconPencil, IconThermometer, IconClock } from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

// Menyesuaikan interface prop dengan model baru
const props = defineProps<{
    density: {
        id: number;
        tgl: string | null;
        spec: string;
        mulai_proses: string;
        selesai_proses: string;
        temp_air: number;
        density_user: { name: string } | null;
        water_absoription_user: { name: string } | null;
    };
}>();
</script>

<template>
    <Head title="Detail Density & Water Absorption" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">

        <!-- Header Action Bar -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child class="rounded-full">
                    <Link :href="route('density.index')">
                        <IconArrowLeft class="size-4" />
                    </Link>
                </Button>
                <h2 class="text-3xl font-bold tracking-tight">Detail Log Pengujian</h2>
            </div>

            <div class="flex items-center gap-2">
                <!-- Arahkan ke rute produk_dwa Anda -->
                <Button as-child variant="outline" size="sm" class="shadow-sm">
                    <Link :href="route('produkdensity.index', props.density.id)">
                        <IconEye class="mr-2 size-4 text-primary" /> Lihat Produk
                    </Link>
                </Button>

                <AlertDialog>
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button variant="ghost" size="icon">
                                <IconDotsVertical class="size-4" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuItem class="cursor-pointer" as-child>
                                <Link :href="route('density.edit', props.density.id)">
                                    <IconPencil class="mr-2 size-4 text-muted-foreground" /> Edit Data
                                </Link>
                            </DropdownMenuItem>
                            <AlertDialogTrigger as-child>
                                <DropdownMenuItem class="text-destructive cursor-pointer">
                                    <IconTrash class="mr-2 size-4" /> Hapus Log
                                </DropdownMenuItem>
                            </AlertDialogTrigger>
                        </DropdownMenuContent>
                    </DropdownMenu>

                    <AlertDialogContent>
                        <AlertDialogHeader>
                            <AlertDialogTitle>Hapus Data?</AlertDialogTitle>
                            <AlertDialogDescription>
                                Apakah Anda yakin ingin menghapus permanen log pengujian tanggal
                                <strong class="text-foreground">
                                    {{ props.density.tgl ? new Date(props.density.tgl).toLocaleDateString("id-ID", { day: "2-digit", month: "long", year: "numeric" }) : '-' }}
                                </strong>? Tindakan ini menghapus seluruh data terkait dan tidak dapat dibatalkan.
                            </AlertDialogDescription>
                        </AlertDialogHeader>
                        <AlertDialogFooter>
                            <AlertDialogCancel>Batal</AlertDialogCancel>
                            <AlertDialogAction @click="router.delete(route('density.destroy', props.density.id))" class="bg-destructive text-white hover:bg-destructive/90">
                                Ya, Hapus
                            </AlertDialogAction>
                        </AlertDialogFooter>
                    </AlertDialogContent>
                </AlertDialog>
            </div>
        </div>

        <!-- Konten Detail Utama -->
        <div class="max-w-4xl grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Card Kiri: Informasi Umum & Personel (Lebar 2 Kolom di Desktop) -->
            <Card class="border-none shadow-md md:col-span-2">
                <CardHeader class="border-b bg-muted/10 pb-4">
                    <div class="flex items-center gap-2">
                        <IconHammer class="size-5 text-primary" />
                        <CardTitle class="text-lg">Informasi Umum & Operator</CardTitle>
                    </div>
                </CardHeader>
                <CardContent class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">ID Record</span>
                        <p class="text-base font-mono font-medium mt-1">#{{ props.density.id }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Tanggal Log</span>
                        <p class="text-base font-medium mt-1">
                            {{ props.density.tgl ? new Date(props.density.tgl).toLocaleDateString("id-ID", { weekday: "long", day: "2-digit", month: "long", year: "numeric" }) : '-' }}
                        </p>
                    </div>
                    <div class="border-t pt-4 sm:border-none sm:pt-0">
                        <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Operator Density</span>
                        <p class="text-base font-medium text-zinc-800 dark:text-zinc-200 mt-1">
                            {{ props.density.density_user?.name ?? 'Tidak ditugaskan' }}
                        </p>
                    </div>
                    <div class="border-t pt-4 sm:border-none sm:pt-0">
                        <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Operator Water Absorption</span>
                        <p class="text-base font-medium text-zinc-800 dark:text-zinc-200 mt-1">
                            {{ props.density.water_absoription_user?.name ?? 'Tidak ditugaskan' }}
                        </p>
                    </div>
                </CardContent>
            </Card>

            <!-- Card Kanan: Parameter & Kondisi Proses (Lebar 1 Kolom) -->
            <Card class="border-none shadow-md bg-zinc-50/50 dark:bg-zinc-900/50">
                <CardHeader class="border-b pb-4">
                    <div class="flex items-center gap-2">
                        <IconClock class="size-5 text-primary" />
                        <CardTitle class="text-lg">Kondisi Proses</CardTitle>
                    </div>
                </CardHeader>
                <CardContent class="p-6 space-y-5">
                    <div>
                        <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider block">Spesifikasi (Spec)</span>
                        <span class="inline-flex items-center rounded-md bg-primary/10 px-2.5 py-0.5 text-sm font-medium text-primary mt-1">
                            {{ props.density.spec }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center border-t pt-3">
                        <div>
                            <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider block">Mulai</span>
                            <p class="text-sm font-medium mt-0.5">{{ props.density.mulai_proses }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider block">Selesai</span>
                            <p class="text-sm font-medium mt-0.5">{{ props.density.selesai_proses }}</p>
                        </div>
                    </div>
                    <div class="border-t pt-4 flex items-center gap-3">
                        <div class="p-2 rounded-md bg-amber-50 dark:bg-amber-950/40 text-amber-600">
                            <IconThermometer class="size-5" />
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider block">Temperatur Air</span>
                            <p class="text-base font-bold text-zinc-800 dark:text-zinc-100">{{ props.density.temp_air }} °C</p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
