<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { IconArrowLeft, IconDeviceFloppy, IconLoader2 } from "@tabler/icons-vue";
import { ref, onMounted, computed, watch } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    waterabsorption: { id: number };
    produkwa: any;
    customers: Array<{ id: number; customer: string }>;
    modelsizes: Array<{ id: number; customer_id: number; modelsize: string }>;
    spesifikasis: Array<{ id: number; spesifikasi: string }>;
}>();

const form = useForm({
    temp: props.produkwa.temp ?? 0,
    palm_wo: parseFloat(props.produkwa.palm_wo) || 0,
    palm_wa: parseFloat(props.produkwa.palm_wa) || 0,
    mc_wo: parseFloat(props.produkwa.mc_wo) || 0,
    mc_wa: parseFloat(props.produkwa.mc_wa) || 0,
    sl_wo: parseFloat(props.produkwa.sl_wo) || 0,
    sl_wa: parseFloat(props.produkwa.sl_wa) || 0,
});

const palmWater = computed(() => parseFloat((form.palm_wa - form.palm_wo).toFixed(3)));
const mcWater = computed(() => parseFloat((form.mc_wa - form.mc_wo).toFixed(3)));
const slWater = computed(() => parseFloat((form.sl_wa - form.sl_wo).toFixed(3)));

const txtCust = ref(""); const txtModel = ref(""); const txtSpec = ref("");

const syncText = () => {
    txtCust.value = props.customers.find(c => c.id === props.produkwa.customer_id)?.customer || "-";
    txtModel.value = props.modelsizes.find(m => m.id === props.produkwa.modelsize_id)?.modelsize || "-";
    txtSpec.value = props.spesifikasis.find(s => s.id === props.produkwa.spesifikasi_id)?.spesifikasi || "-";
};

onMounted(() => syncText());
watch(() => props.produkwa, (newP) => {
    form.temp = newP.temp;
    form.palm_wo = parseFloat(newP.palm_wo) || 0; form.palm_wa = parseFloat(newP.palm_wa) || 0;
    form.mc_wo = parseFloat(newP.mc_wo) || 0; form.mc_wa = parseFloat(newP.mc_wa) || 0;
    form.sl_wo = parseFloat(newP.sl_wo) || 0; form.sl_wa = parseFloat(newP.sl_wa) || 0;
    syncText();
});
</script>

<template>
    <Head title="Pengerjaan Lab Water Absorption" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child class="rounded-full">
                <Link :href="route('produkwa.index', props.waterabsorption.id)"><IconArrowLeft class="size-4" /></Link>
            </Button>
            <h2 class="text-3xl font-bold tracking-tight">Input Cepat Nilai Lab</h2>
        </div>

        <div class="max-w-4xl">
            <Card class="border-none shadow-lg">
                <CardHeader class="bg-zinc-50 dark:bg-zinc-900 border-b p-4">
                    <CardTitle class="text-sm font-medium text-muted-foreground flex justify-between">
                        <span>Sampel: {{ props.produkwa.sampel }} | {{ txtCust }} ({{ txtModel }})</span>
                        <span class="text-blue-600 font-bold">Specs: {{ txtSpec }}</span>
                    </CardTitle>
                </CardHeader>
                <CardContent class="p-6">
                    <form @submit.prevent="form.put(route('produkwa.update', [props.waterabsorption.id, props.produkwa.id]))" class="space-y-6">
                        <div class="w-1/3"><Label>Suhu Temp (°C)</Label><Input type="number" v-model="form.temp" class="h-11" /></div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Palm Matrix -->
                            <div class="p-4 rounded-xl border bg-zinc-50/50 dark:bg-zinc-900/50 space-y-3">
                                <Label class="font-bold text-zinc-500">PALM MATRICES</Label>
                                <div><Label>WO (gr)</Label><Input type="number" step="0.001" v-model="form.palm_wo" /></div>
                                <div><Label>WA (gr)</Label><Input type="number" step="0.001" v-model="form.palm_wa" /></div>
                                <div class="pt-2 text-xs font-bold flex justify-between"><span>Water Net:</span><span class="text-base text-primary">{{ palmWater }}</span></div>
                            </div>
                            <!-- MC Matrix -->
                            <div class="p-4 rounded-xl border bg-blue-50/30 dark:bg-blue-950/10 space-y-3">
                                <Label class="font-bold text-blue-500">MC MATRICES</Label>
                                <div><Label>WO (gr)</Label><Input type="number" step="0.001" v-model="form.mc_wo" /></div>
                                <div><Label>WA (gr)</Label><Input type="number" step="0.001" v-model="form.mc_wa" /></div>
                                <div class="pt-2 text-xs font-bold flex justify-between"><span>Water Net:</span><span class="text-base text-blue-600">{{ mcWater }}</span></div>
                            </div>
                            <!-- SL Matrix -->
                            <div class="p-4 rounded-xl border bg-amber-50/30 dark:bg-amber-950/10 space-y-3">
                                <Label class="font-bold text-amber-500">SL MATRICES</Label>
                                <div><Label>WO (gr)</Label><Input type="number" step="0.001" v-model="form.sl_wo" /></div>
                                <div><Label>WA (gr)</Label><Input type="number" step="0.001" v-model="form.sl_wa" /></div>
                                <div class="pt-2 text-xs font-bold flex justify-between"><span>Water Net:</span><span class="text-base text-amber-600">{{ slWater }}</span></div>
                            </div>
                        </div>

                        <Button type="submit" class="w-full h-12 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold">
                            <IconDeviceFloppy class="mr-2" /> Simpan Hasil Pengujian
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
