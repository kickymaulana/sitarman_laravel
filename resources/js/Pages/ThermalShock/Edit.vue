<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { IconArrowLeft, IconDeviceFloppy, IconDotsVertical, IconTrash } from "@tabler/icons-vue";
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from "@/components/ui/alert-dialog";
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu";
import { ref, computed, onMounted } from "vue";
import { onClickOutside } from "@vueuse/core";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    thermalshock: any;
    ovens: Array<{ id: number; thermal_oven: string }>;
    pintus: Array<{ id: number; thermal_pintu: string }>;
}>();

const form = useForm({
    thermal_oven_id: props.thermalshock.thermal_oven_id,
    thermal_pintu_id: props.thermalshock.thermal_pintu_id,
    hari_tgl: props.thermalshock.hari_tgl,
    suhu_testing: props.thermalshock.suhu_testing,
    suhu_motor: props.thermalshock.suhu_motor,
    suhu_display: props.thermalshock.suhu_display,
    suhu_actual: props.thermalshock.suhu_actual,
    jam_awal_proses: props.thermalshock.jam_awal_proses,
    jam_capai_suhu: props.thermalshock.jam_capai_suhu,
    suhu_awal: props.thermalshock.suhu_awal,
    suhu_air: props.thermalshock.suhu_air,
    jam_mulai_tembak: props.thermalshock.jam_mulai_tembak,
    jam_selesai_tembak: props.thermalshock.jam_selesai_tembak
});

const searchOven = ref("");
const showOvenDropdown = ref(false);
const searchPintu = ref("");
const showPintuDropdown = ref(false);

onMounted(() => {
    const oven = props.ovens.find(o => o.id === form.thermal_oven_id);
    if (oven) searchOven.value = oven.thermal_oven;

    const pintu = props.pintus.find(p => p.id === form.thermal_pintu_id);
    if (pintu) searchPintu.value = pintu.thermal_pintu;
});
</script>

<template>
    <Head title="Edit Thermal Shock" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child class="rounded-full">
                    <Link :href="route('thermalshock.index')"><IconArrowLeft class="size-4" /></Link>
                </Button>
                <h2 class="text-3xl font-bold tracking-tight">Edit Thermal Shock</h2>
            </div>
        </div>

        <div class="max-w-4xl">
            <Card class="border-none shadow-lg">
                <CardHeader class="flex flex-row items-center justify-between border-b">
                    <CardTitle class="text-primary text-lg">Update Data ID: {{ props.thermalshock.id }}</CardTitle>
                    <AlertDialog>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="ghost" size="icon"><IconDotsVertical class="size-4" /></Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <AlertDialogTrigger as-child>
                                    <DropdownMenuItem class="text-destructive"><IconTrash class="mr-2 size-4" />Hapus</DropdownMenuItem>
                                </AlertDialogTrigger>
                            </DropdownMenuContent>
                        </DropdownMenu>
                        <AlertDialogContent>
                            <AlertDialogHeader>
                                <AlertDialogTitle>Hapus Data?</AlertDialogTitle>
                                <AlertDialogDescription>Hapus permanen log thermal shock tanggal {{ props.thermalshock.hari_tgl }}?</AlertDialogDescription>
                            </AlertDialogHeader>
                            <AlertDialogFooter>
                                <AlertDialogCancel>Batal</AlertDialogCancel>
                                <AlertDialogAction @click="router.delete(route('thermalshock.destroy', props.thermalshock.id))" class="bg-destructive text-white">Ya, Hapus</AlertDialogAction>
                            </AlertDialogFooter>
                        </AlertDialogContent>
                    </AlertDialog>
                </CardHeader>

                <CardContent class="pt-6">
                    <form @submit.prevent="form.put(route('thermalshock.update', props.thermalshock.id))" class="space-y-6">
                        <!-- Komponen Form Input disamakan persis strukturnya dengan Create.vue di atas -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label>Thermal Oven</Label>
                                <Input v-model="searchOven" @focus="showOvenDropdown = true" />
                            </div>
                            <div class="grid gap-2">
                                <Label>Thermal Pintu</Label>
                                <Input v-model="searchPintu" @focus="showPintuDropdown = true" />
                            </div>
                        </div>

                        <!-- Parameter Field lainnya -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="grid gap-2">
                                <Label>Hari / Tanggal</Label>
                                <Input type="date" v-model="form.hari_tgl" />
                            </div>
                            <div class="grid gap-2">
                                <Label>Suhu Testing</Label>
                                <Input type="number" v-model="form.suhu_testing" />
                            </div>
                            <div class="grid gap-2">
                                <Label>Suhu Motor</Label>
                                <Input v-model="form.suhu_motor" />
                            </div>
                        </div>

                        <Button type="submit" :disabled="form.processing" class="w-full bg-primary">
                            <IconDeviceFloppy class="mr-2 size-4" />Simpan Perubahan
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
