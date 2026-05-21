<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { IconArrowLeft, IconDeviceFloppy, IconLoader2 } from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const form = useForm({
    tgl_test: "",
    spec: "",
    mulai_proses: "",
    selesai_proses: "",
    temp_air: ""
});

// Auto Format Waktu (HH:mm)
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
</script>

<template>
    <Head title="Tambah Water Absorption" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child class="rounded-full">
                <Link :href="route('waterabsorption.index')">
                    <IconArrowLeft class="size-4" />
                </Link>
            </Button>
            <h2 class="text-3xl font-bold tracking-tight">Tambah Water Absorption</h2>
        </div>

        <div class="max-w-4xl">
            <Card class="border-none shadow-lg">
                <CardHeader>
                    <CardTitle class="text-primary">Master Data Water Absorption</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="form.post(route('waterabsorption.store'))" class="space-y-6">

                        <!-- Row 1: Tanggal & Spec -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="tgl_test">Tanggal Test</Label>
                                <Input type="date" id="tgl_test" v-model="form.tgl_test" />
                                <p v-if="form.errors.tgl_test" class="text-sm text-destructive">{{ form.errors.tgl_test }}</p>
                            </div>
                            <div class="grid gap-2">
                                <Label for="spec">Spesifikasi (Spec)</Label>
                                <Input type="text" id="spec" v-model="form.spec" placeholder="Masukkan spesifikasi..." />
                                <p v-if="form.errors.spec" class="text-sm text-destructive">{{ form.errors.spec }}</p>
                            </div>
                        </div>

                        <!-- Row 2: Waktu Proses & Temp Air -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                                <Input type="number" id="temp_air" v-model="form.temp_air" placeholder="Contoh: 30" />
                                <p v-if="form.errors.temp_air" class="text-sm text-destructive">{{ form.errors.temp_air }}</p>
                            </div>
                        </div>

                        <Button type="submit" :disabled="form.processing" class="w-full bg-primary hover:bg-primary/90 mt-4 shadow-md">
                            <IconLoader2 v-if="form.processing" class="mr-2 animate-spin" />
                            <IconDeviceFloppy v-else class="mr-2" /> Simpan Data WA
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
