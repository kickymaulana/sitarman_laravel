<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { IconFlame, IconArrowLeft, IconCheck } from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    thermalshocks: Array<{
        id: number;
        hari_tgl: string;
        posisi_former: number;
        thermal_pintu: { thermal_pintu: string } | null;
        customer: { customer: string } | null;
    }>;
    selectedIds: Array<number>;
}>();

const form = useForm({
    ids: props.selectedIds,
    suhu_awal_200: 0,
    suhu_display_200: 0,
    suhu_actual_200: 0,
});

const submit = () => {
    form.put(route('thermalshock.bulkUpdate200'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Bulk Edit Parameter Suhu 200°C" />

    <div class="max-w-3xl mx-auto p-4 md:p-8">
        <div class="mb-4">
            <Button as-child variant="ghost" size="sm">
                <Link :href="route('thermalshock.index')" class="flex items-center gap-1.5 text-xs text-muted-foreground">
                    <IconArrowLeft class="size-4" /> Kembali ke Daftar
                </Link>
            </Button>
        </div>

        <Card class="shadow-md border border-amber-100 dark:border-amber-950">
            <CardHeader class="space-y-1 bg-amber-50/50 dark:bg-amber-950/10 pb-6 rounded-t-lg">
                <CardTitle class="text-lg font-bold flex items-center gap-2 text-amber-700 dark:text-amber-400">
                    <IconFlame class="size-5" />
                    Isi Parameter Pengujian Massal Suhu 200°C
                </CardTitle>
                <CardDescription>
                    Nilai yang Anda masukkan di bawah ini akan langsung diterapkan ke <strong>{{ selectedIds.length }} record</strong> thermal shock sekaligus.
                </CardDescription>
            </CardHeader>

            <CardContent class="pt-6">
                <form @submit.prevent="submit" class="space-y-6">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-2">
                            <Label for="suhu_awal_200">Suhu Awal 200°C (°C)</Label>
                            <Input id="suhu_awal_200" type="number" v-model.number="form.suhu_awal_200" required min="0" />
                            <span v-if="form.errors.suhu_awal_200" class="text-xs text-destructive">{{ form.errors.suhu_awal_200 }}</span>
                        </div>

                        <div class="space-y-2">
                            <Label for="suhu_display_200">Suhu Display 200°C (°C)</Label>
                            <Input id="suhu_display_200" type="number" v-model.number="form.suhu_display_200" required min="0" />
                            <span v-if="form.errors.suhu_display_200" class="text-xs text-destructive">{{ form.errors.suhu_display_200 }}</span>
                        </div>

                        <div class="space-y-2">
                            <Label for="suhu_actual_200">Suhu Actual 200°C (°C)</Label>
                            <Input id="suhu_actual_200" type="number" v-model.number="form.suhu_actual_200" required min="0" />
                            <span v-if="form.errors.suhu_actual_200" class="text-xs text-destructive">{{ form.errors.suhu_actual_200 }}</span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label class="text-xs text-muted-foreground">Daftar Baris Target Update ({{ thermalshocks.length }} data):</Label>
                        <div class="border rounded-md max-h-48 overflow-y-auto text-xs divide-y bg-zinc-50/50 dark:bg-zinc-900/50">
                            <div v-for="item in thermalshocks" :key="item.id" class="p-2.5 flex justify-between items-center">
                                <div>
                                    <span class="font-semibold text-primary">Pos {{ item.posisi_former }}</span> -
                                    <span>{{ item.customer?.customer ?? 'No Customer' }}</span>
                                </div>
                                <div class="text-muted-foreground text-[11px]">
                                    {{ item.thermal_pintu?.thermal_pintu }} | {{ item.hari_tgl }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t">
                        <Button as-child variant="outline" type="button" class="text-xs">
                            <Link :href="route('thermalshock.index')">Batal</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing" class="bg-amber-600 hover:bg-amber-700 text-white text-xs">
                            <IconCheck class="mr-1.5 size-4" />
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan Massal' }}
                        </Button>
                    </div>

                </form>
            </CardContent>
        </Card>
    </div>
</template>
