<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from "@/components/ui/alert-dialog";
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu";
import { IconArrowLeft, IconEye, IconTrash, IconDotsVertical, IconDroplet, IconClock, IconThermometer, IconPencil } from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    waterabsorption: {
        id: number;
        tgl_test: string;
        spec: string;
        mulai_proses: string;
        selesai_proses: string;
        temp_air: number;
        user: { name: string } | null;
    };
}>();

const formatTime = (timeString: string | null | undefined) => {
    if (!timeString) return "-";
    return timeString.substring(0, 5);
};
</script>

<template>
    <Head title="Detail Water Absorption" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">

        <!-- Header Aksi -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child class="rounded-full">
                    <Link :href="route('waterabsorption.index')">
                        <IconArrowLeft class="size-4" />
                    </Link>
                </Button>
                <h2 class="text-3xl font-bold tracking-tight">Detail Water Absorption</h2>
            </div>

            <div class="flex items-center gap-2">
                <Button as-child variant="outline" size="sm" class="shadow-sm">
                    <Link :href="route('produkwa.index', props.waterabsorption.id)">
                        <IconEye class="mr-2 size-4 text-primary" /> Produk
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
                                <Link :href="route('waterabsorption.edit', props.waterabsorption.id)">
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
                                Apakah Anda yakin ingin menghapus permanen log water absorption tanggal
                                <strong class="text-foreground">
                                    {{ props.waterabsorption.tgl_test ? new Date(props.waterabsorption.tgl_test).toLocaleDateString("id-ID", { day: "2-digit", month: "long", year: "numeric" }) : '-' }}
                                </strong>? Tindakan ini tidak dapat dibatalkan.
                            </AlertDialogDescription>
                        </AlertDialogHeader>
                        <AlertDialogFooter>
                            <AlertDialogCancel>Batal</AlertDialogCancel>
                            <AlertDialogAction @click="router.delete(route('waterabsorption.destroy', props.waterabsorption.id))" class="bg-destructive text-white hover:bg-destructive/90">
                                Ya, Hapus
                            </AlertDialogAction>
                        </AlertDialogFooter>
                    </AlertDialogContent>
                </AlertDialog>
            </div>
        </div>

        <!-- Konten Utama -->
        <div class="max-w-4xl grid grid-cols-1 gap-6">
            <!-- Card Informasi Umum -->
            <Card class="border-none shadow-lg">
                <CardHeader class="border-b bg-muted/20 pb-4">
                    <div class="flex items-center gap-2">
                        <IconDroplet class="size-5 text-primary" />
                        <CardTitle class="text-lg">Informasi Pengujian</CardTitle>
                    </div>
                </CardHeader>
                <CardContent class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div>
                        <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Tanggal Test</span>
                        <p class="text-base font-medium mt-1">
                            {{ props.waterabsorption.tgl_test ? new Date(props.waterabsorption.tgl_test).toLocaleDateString("id-ID", { weekday: "long", day: "2-digit", month: "long", year: "numeric" }) : '-' }}
                        </p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Spesifikasi (Spec)</span>
                        <p class="text-base font-bold text-primary mt-1">
                            {{ props.waterabsorption.spec }}
                        </p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Operator (Input Oleh)</span>
                        <p class="text-base font-medium text-zinc-700 dark:text-zinc-300 mt-1">
                            {{ props.waterabsorption.user?.name ?? 'Tidak diketahui' }}
                        </p>
                    </div>
                </CardContent>
            </Card>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Card Parameter Suhu -->
                <Card class="border-none shadow-lg">
                    <CardHeader class="border-b bg-muted/20 pb-4">
                        <div class="flex items-center gap-2">
                            <IconThermometer class="size-5 text-blue-500" />
                            <CardTitle class="text-lg">Kondisi Air</CardTitle>
                        </div>
                    </CardHeader>
                    <CardContent class="p-6">
                        <div>
                            <span class="text-xs font-medium text-muted-foreground">Temperatur Air</span>
                            <p class="text-2xl font-bold text-blue-600 mt-1">{{ props.waterabsorption.temp_air }}°C</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Card Waktu Proses -->
                <Card class="border-none shadow-lg">
                    <CardHeader class="border-b bg-muted/20 pb-4">
                        <div class="flex items-center gap-2">
                            <IconClock class="size-5 text-zinc-500" />
                            <CardTitle class="text-lg">Durasi Pengujian</CardTitle>
                        </div>
                    </CardHeader>
                    <CardContent class="p-6 space-y-4">
                        <div class="flex justify-between items-center border-b pb-2">
                            <span class="text-sm font-medium text-muted-foreground">Jam Mulai Proses</span>
                            <span class="text-base font-semibold bg-secondary px-2.5 py-0.5 rounded text-secondary-foreground">
                                {{ formatTime(props.waterabsorption.mulai_proses) }} WIB
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-muted-foreground">Jam Selesai Proses</span>
                            <span class="text-base font-semibold bg-primary/10 text-primary px-2.5 py-0.5 rounded">
                                {{ formatTime(props.waterabsorption.selesai_proses) }} WIB
                            </span>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
