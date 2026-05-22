<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { IconArrowLeft, IconDeviceFloppy, IconDotsVertical, IconTrash, IconLoader2 } from "@tabler/icons-vue";
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from "@/components/ui/alert-dialog";
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu";
import { onMounted } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    waterabsorption: {
        id: number;
        tgl: string | null;
        spec: string;
        mulai_proses: string;
        selesai_proses: string;
        temp_air: number;
        density_user_id: number;
        water_absoription_user_id: number;
    };
    users: Array<{ id: number; name: string }>;
}>();

const form = useForm({
    tgl: props.waterabsorption.tgl || "", // Menggunakan field tgl baru
    spec: props.waterabsorption.spec ?? "-",
    density_user_id: props.waterabsorption.density_user_id ?? "",
    water_absoription_user_id: props.waterabsorption.water_absoription_user_id ?? "",
    mulai_proses: props.waterabsorption.mulai_proses || "00:00",
    selesai_proses: props.waterabsorption.selesai_proses || "00:00",
    temp_air: props.waterabsorption.temp_air ?? 0
});

const formatTimeInput = (field: keyof typeof form, event: Event) => {
    const target = event.target as HTMLInputElement;
    let val = target.value.replace(/\D/g, '');

    if (val.length > 4) val = val.substring(0, 4);

    if (val.length > 2) {
        let hours = val.substring(0, 2);
        if (parseInt(hours) > 23) hours = '23';

        let minutes = val.substring(2);
        if (parseInt(minutes) > 59) minutes = '59';

        val = hours + ':' + minutes;
    } else if (val.length === 2 && parseInt(val) > 23) {
        val = '23';
    }

    // @ts-ignore
    form[field] = val;
};

onMounted(() => {
    if (form.mulai_proses) form.mulai_proses = form.mulai_proses.substring(0, 5);
    if (form.selesai_proses) form.selesai_proses = form.selesai_proses.substring(0, 5);
});
</script>

<template>
    <Head title="Edit Water Absorption" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child class="rounded-full">
                    <Link :href="route('waterabsorption.show', props.waterabsorption.id)"><IconArrowLeft class="size-4" /></Link>
                </Button>
                <h2 class="text-3xl font-bold tracking-tight">Edit Water Absorption</h2>
            </div>
        </div>

        <div class="max-w-4xl">
            <Card class="border-none shadow-lg">
                <CardHeader class="flex flex-row items-center justify-between border-b pb-4 mb-4">
                    <CardTitle class="text-primary text-lg">Update Data ID: {{ props.waterabsorption.id }}</CardTitle>
                    <AlertDialog>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="ghost" size="icon"><IconDotsVertical class="size-4" /></Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <AlertDialogTrigger as-child>
                                    <DropdownMenuItem class="text-destructive cursor-pointer"><IconTrash class="mr-2 size-4" />Hapus Record</DropdownMenuItem>
                                </AlertDialogTrigger>
                            </DropdownMenuContent>
                        </DropdownMenu>
                        <AlertDialogContent>
                            <AlertDialogHeader>
                                <AlertDialogTitle>Hapus Data?</AlertDialogTitle>
                                <AlertDialogDescription>
                                    Hapus permanen log water absorption tanggal
                                    <strong>{{ props.waterabsorption.tgl ? new Date(props.waterabsorption.tgl).toLocaleDateString("id-ID", { day: "2-digit", month: "long", year: "numeric" }) : '-' }}</strong>?
                                </AlertDialogDescription>
                            </AlertDialogHeader>
                            <AlertDialogFooter>
                                <AlertDialogCancel>Batal</AlertDialogCancel>
                                <AlertDialogAction @click="router.delete(route('waterabsorption.destroy', props.waterabsorption.id))" class="bg-destructive text-white hover:bg-destructive/90">Ya, Hapus</AlertDialogAction>
                            </AlertDialogFooter>
                        </AlertDialogContent>
                    </AlertDialog>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="form.put(route('waterabsorption.update', props.waterabsorption.id))" class="space-y-6">

                        <!-- Row 1: Tanggal & Spec -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="tgl">Tanggal Test</Label>
                                <Input type="date" id="tgl" v-model="form.tgl" />
                                <p v-if="form.errors.tgl" class="text-sm text-destructive">{{ form.errors.tgl }}</p>
                            </div>
                            <div class="grid gap-2">
                                <Label for="spec">Spesifikasi (Spec)</Label>
                                <Input type="text" id="spec" v-model="form.spec" />
                                <p v-if="form.errors.spec" class="text-sm text-destructive">{{ form.errors.spec }}</p>
                            </div>
                        </div>

                        <!-- Row 2: Operator (Density & WA) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t pt-4">
                            <div class="grid gap-2">
                                <Label for="water_absoription_user_id">Operator Water Absorption</Label>
                                <select id="water_absoription_user_id" v-model="form.water_absoription_user_id" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                                    <option value="" disabled>-- Pilih Operator WA --</option>
                                    <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                                </select>
                                <p v-if="form.errors.water_absoription_user_id" class="text-sm text-destructive">{{ form.errors.water_absoription_user_id }}</p>
                            </div>
                            <div class="grid gap-2">
                                <Label for="density_user_id">Operator Density</Label>
                                <select id="density_user_id" v-model="form.density_user_id" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                                    <option value="" disabled>-- Pilih Operator Density --</option>
                                    <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                                </select>
                                <p v-if="form.errors.density_user_id" class="text-sm text-destructive">{{ form.errors.density_user_id }}</p>
                            </div>
                        </div>

                        <!-- Row 3: Waktu Proses & Temp Air -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t pt-4">
                            <div class="grid gap-2">
                                <Label for="mulai_proses">Jam Mulai Proses</Label>
                                <Input type="text" id="mulai_proses" v-model="form.mulai_proses" @input="formatTimeInput('mulai_proses', $event)" placeholder="00:00" />
                                <span class="text-[10px] text-muted-foreground italic">Format 24 Jam (Contoh: 08:00)</span>
                                <p v-if="form.errors.mulai_proses" class="text-sm text-destructive">{{ form.errors.mulai_proses }}</p>
                            </div>
                            <div class="grid gap-2">
                                <Label for="selesai_proses">Jam Selesai Proses</Label>
                                <Input type="text" id="selesai_proses" v-model="form.selesai_proses" @input="formatTimeInput('selesai_proses', $event)" placeholder="00:00" />
                                <span class="text-[10px] text-muted-foreground italic">Format 24 Jam (Contoh: 12:00)</span>
                                <p v-if="form.errors.selesai_proses" class="text-sm text-destructive">{{ form.errors.selesai_proses }}</p>
                            </div>
                            <div class="grid gap-2">
                                <Label for="temp_air">Temperatur Air (°C)</Label>
                                <Input type="number" id="temp_air" v-model.number="form.temp_air" />
                                <p v-if="form.errors.temp_air" class="text-sm text-destructive">{{ form.errors.temp_air }}</p>
                            </div>
                        </div>

                        <Button type="submit" :disabled="form.processing" class="w-full bg-primary hover:bg-primary/90 mt-4 shadow-md">
                            <IconLoader2 v-if="form.processing" class="mr-2 animate-spin" />
                            <IconDeviceFloppy v-else class="mr-2" /> Simpan Perubahan
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
