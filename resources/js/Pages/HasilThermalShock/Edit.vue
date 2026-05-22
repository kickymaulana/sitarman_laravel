<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent } from "@/components/ui/card";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { IconArrowLeft, IconDeviceFloppy, IconLoader2, IconFlame, IconDroplet, IconSettings, IconTrash, IconDotsVertical } from "@tabler/icons-vue";
import { ref, computed, watch, onMounted } from "vue";
import { onClickOutside } from "@vueuse/core";
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from "@/components/ui/alert-dialog";
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    hasil: any;
    ovens: Array<{ id: number; oven: string }>;
    jamKeluarOvens: Array<{ id: number; jam_keluar_oven: string }>;
    customers: Array<{ id: number; customer: string }>;
    modelsizes: Array<{ id: number; customer_id: number; modelsize: string }>;
    spesifikasis: Array<{ id: number; spesifikasi: string }>;
    tinggiFormers: Array<{ id: number; tinggi_former: number }>;
}>();

// Inisialisasi useForm mengikat langsung ke data prop 'hasil'
const form = useForm({
    tanggal_keluar_oven: props.hasil?.tanggal_keluar_oven || "",
    oven_id: props.hasil?.oven_id || "",
    jam_keluar_oven_id: props.hasil?.jam_keluar_oven_id || "",
    customer_id: props.hasil?.customer_id || "",
    modelsize_id: props.hasil?.modelsize_id || "",
    spesifikasi_id: props.hasil?.spesifikasi_id || "",
    tinggi_former_id: props.hasil?.tinggi_former_id || "",

    kode_tanah: props.hasil?.kode_tanah || "",
    suhu_180: props.hasil?.suhu_180 || "Belum Tes",
    suhu_200: props.hasil?.suhu_200 || "Belum Tes",
    suhu: props.hasil?.suhu || 0,
    berat_former: props.hasil?.berat_former || 0,

    thickness: parseFloat(props.hasil?.thickness) || 0,
    chemical: parseFloat(props.hasil?.chemical) || 0,
    wa_palm: parseFloat(props.hasil?.wa_palm) || 0,
    wa_mc: parseFloat(props.hasil?.wa_mc) || 0,
    wa_sli: parseFloat(props.hasil?.wa_sli) || 0,
    density: parseFloat(props.hasil?.density) || 0,
    luas_area: parseFloat(props.hasil?.luas_area) || 0,
    visual: props.hasil?.visual || 0,
});

// Dropdown State Managers
const searchOven = ref("");   const showOvenDrop = ref(false);   const ovenRef = ref(null);
const searchJam = ref("");    const showJamDrop = ref(false);    const jamRef = ref(null);
const searchCust = ref("");   const showCustDrop = ref(false);   const custRef = ref(null);
const searchModel = ref("");  const showModelDrop = ref(false);  const modelRef = ref(null);
const searchSpec = ref("");   const showSpecDrop = ref(false);   const specRef = ref(null);
const searchTinggi = ref(""); const showTinggiDrop = ref(false); const tinggiRef = ref(null);

onMounted(() => {
    if (form.oven_id) searchOven.value = props.ovens.find(o => o.id === form.oven_id)?.oven || "";
    if (form.jam_keluar_oven_id) searchJam.value = props.jamKeluarOvens.find(j => j.id === form.jam_keluar_oven_id)?.jam_keluar_oven || "";
    if (form.customer_id) searchCust.value = props.customers.find(c => c.id === form.customer_id)?.customer || "";
    if (form.modelsize_id) searchModel.value = props.modelsizes.find(m => m.id === form.modelsize_id)?.modelsize || "";
    if (form.spesifikasi_id) searchSpec.value = props.spesifikasis.find(s => s.id === form.spesifikasi_id)?.spesifikasi || "";
    if (form.tinggi_former_id) searchTinggi.value = String(props.tinggiFormers.find(t => t.id === form.tinggi_former_id)?.tinggi_former || "");
});

onClickOutside(ovenRef, () => { showOvenDrop.value = false; searchOven.value = props.ovens.find(o => o.id === form.oven_id)?.oven || "" });
onClickOutside(jamRef, () => { showJamDrop.value = false; searchJam.value = props.jamKeluarOvens.find(j => j.id === form.jam_keluar_oven_id)?.jam_keluar_oven || "" });
onClickOutside(custRef, () => { showCustDrop.value = false; searchCust.value = props.customers.find(c => c.id === form.customer_id)?.customer || "" });
onClickOutside(modelRef, () => { showModelDrop.value = false; searchModel.value = props.modelsizes.find(m => m.id === form.modelsize_id)?.modelsize || "" });
onClickOutside(specRef, () => { showSpecDrop.value = false; searchSpec.value = props.spesifikasis.find(s => s.id === form.spesifikasi_id)?.spesifikasi || "" });
onClickOutside(tinggiRef, () => { showTinggiDrop.value = false; searchTinggi.value = String(props.tinggiFormers.find(t => t.id === form.tinggi_former_id)?.tinggi_former || "") });

const filteredOvens = computed(() => props.ovens.filter(o => o.oven.toLowerCase().includes(searchOven.value.toLowerCase())));
const filteredJams = computed(() => props.jamKeluarOvens.filter(j => j.jam_keluar_oven.toLowerCase().includes(searchJam.value.toLowerCase())));
const filteredCusts = computed(() => props.customers.filter(c => c.customer.toLowerCase().includes(searchCust.value.toLowerCase())));
const filteredModels = computed(() => {
    const subset = form.customer_id ? props.modelsizes.filter(m => m.customer_id === form.customer_id) : props.modelsizes;
    return subset.filter(m => m.modelsize.toLowerCase().includes(searchModel.value.toLowerCase()));
});
const filteredSpecs = computed(() => props.spesifikasis.filter(s => s.spesifikasi.toLowerCase().includes(searchSpec.value.toLowerCase())));
const filteredTinggis = computed(() => props.tinggiFormers.filter(t => String(t.tinggi_former).includes(searchTinggi.value)));

watch(() => form.customer_id, (newVal, oldVal) => {
    if (oldVal) { form.modelsize_id = ""; searchModel.value = ""; }
});
</script>

<template>
    <Head title="Edit Rekap Thermal Shock" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child class="rounded-full">
                    <Link :href="route('hasilthermalshock.index')"><IconArrowLeft class="size-4" /></Link>
                </Button>
                <h2 class="text-3xl font-bold tracking-tight">Edit Rekap Hasil Thermal Shock</h2>
            </div>

            <!-- Danger Zone Management (Hapus Data) -->
            <AlertDialog>
                <DropdownMenu>
                    <DropdownMenuTrigger as-child><Button variant="ghost" size="icon"><IconDotsVertical class="size-4" /></Button></DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <AlertDialogTrigger as-child><DropdownMenuItem class="text-destructive"><IconTrash class="mr-2 size-4" />Hapus Rekap</DropdownMenuItem></AlertDialogTrigger>
                    </DropdownMenuContent>
                </DropdownMenu>
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Konfirmasi Hapus Data?</AlertDialogTitle>
                        <AlertDialogDescription>Data rekap pengujian kode tanah <strong>{{ props.hasil.kode_tanah }}</strong> akan dihapus permanen dari sistem.</AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel>Batal</AlertDialogCancel>
                        <AlertDialogAction @click="router.delete(route('hasilthermalshock.show.destroy', props.hasil.id))" class="bg-destructive text-white">Ya, Hapus</AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </div>

        <div class="max-w-5xl">
            <Card class="border-none shadow-lg">
                <CardContent class="p-6">
                    <form @submit.prevent="form.put(route('hasilthermalshock.show.update', props.hasil.id))" class="space-y-6">

                        <!-- SECTION 1: IDENTITAS & LOKASI OVEN -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-bold uppercase text-zinc-400 flex items-center gap-2">
                                <IconSettings class="size-4" /> Identitas & Lokasi Oven
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="grid gap-2">
                                    <Label>Tanggal Keluar Oven</Label>
                                    <Input type="date" v-model="form.tanggal_keluar_oven" />
                                    <p v-if="form.errors.tanggal_keluar_oven" class="text-xs text-destructive">{{ form.errors.tanggal_keluar_oven }}</p>
                                </div>

                                <div class="grid gap-2 relative" ref="ovenRef">
                                    <Label>Oven</Label>
                                    <Input v-model="searchOven" @focus="showOvenDrop = true" />
                                    <div v-if="showOvenDrop" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 shadow-md dark:bg-zinc-900">
                                        <div v-for="o in filteredOvens" :key="o.id" @click="form.oven_id = o.id; searchOven = o.oven; showOvenDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">{{ o.oven }}</div>
                                    </div>
                                </div>

                                <div class="grid gap-2 relative" ref="jamRef">
                                    <Label>Jam Keluar Oven</Label>
                                    <Input v-model="searchJam" @focus="showJamDrop = true" />
                                    <div v-if="showJamDrop" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 shadow-md dark:bg-zinc-900">
                                        <div v-for="j in filteredJams" :key="j.id" @click="form.jam_keluar_oven_id = j.id; searchJam = j.jam_keluar_oven; showJamDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">{{ j.jam_keluar_oven }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="grid gap-2 relative" ref="custRef">
                                    <Label>Customer</Label>
                                    <Input v-model="searchCust" @focus="showCustDrop = true" />
                                    <div v-if="showCustDrop" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 shadow-md dark:bg-zinc-900">
                                        <div v-for="c in filteredCusts" :key="c.id" @click="form.customer_id = c.id; searchCust = c.customer; showCustDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">{{ c.customer }}</div>
                                    </div>
                                </div>

                                <div class="grid gap-2 relative" ref="modelRef">
                                    <Label>Model Size</Label>
                                    <Input v-model="searchModel" @focus="showModelDrop = true" :disabled="!form.customer_id" />
                                    <div v-if="showModelDrop && form.customer_id" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 shadow-md dark:bg-zinc-900">
                                        <div v-for="m in filteredModels" :key="m.id" @click="form.modelsize_id = m.id; searchModel = m.modelsize; showModelDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">{{ m.modelsize }}</div>
                                    </div>
                                </div>

                                <div class="grid gap-2 relative" ref="specRef">
                                    <Label>Spesifikasi</Label>
                                    <Input v-model="searchSpec" @focus="showSpecDrop = true" />
                                    <div v-if="showSpecDrop" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 shadow-md dark:bg-zinc-900">
                                        <div v-for="s in filteredSpecs" :key="s.id" @click="form.spesifikasi_id = s.id; searchSpec = s.spesifikasi; showSpecDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">{{ s.spesifikasi }}</div>
                                    </div>
                                </div>

                                <div class="grid gap-2 relative" ref="tinggiRef">
                                    <Label>Tinggi Former</Label>
                                    <Input v-model="searchTinggi" @focus="showTinggiDrop = true" />
                                    <div v-if="showTinggiDrop" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 shadow-md dark:bg-zinc-900">
                                        <div v-for="t in filteredTinggis" :key="t.id" @click="form.tinggi_former_id = t.id; searchTinggi = String(t.tinggi_former); showTinggiDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">{{ t.tinggi_former }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="border-zinc-100 dark:border-zinc-800" />

                        <!-- SECTION 2: PARAMETER UJI PANAS -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-bold uppercase text-red-500 flex items-center gap-2">
                                <IconFlame class="size-4" /> Parameter Pengujian Thermal Shock
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                <div class="grid gap-2"><Label>Kode Tanah</Label><Input v-model="form.kode_tanah" /></div>
                                <div class="grid gap-2">
                                    <Label>Hasil Uji 180°C</Label>
                                    <Select v-model="form.suhu_180">
                                        <SelectTrigger><SelectValue /></SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="Belum Tes">Belum Tes</SelectItem>
                                            <SelectItem value="OK">OK</SelectItem>
                                            <SelectItem value="NG">NG</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div class="grid gap-2">
                                    <Label>Hasil Uji 200°C</Label>
                                    <Select v-model="form.suhu_200">
                                        <SelectTrigger><SelectValue /></SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="Belum Tes">Belum Tes</SelectItem>
                                            <SelectItem value="OK">OK</SelectItem>
                                            <SelectItem value="NG">NG</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div class="grid gap-2"><Label>Suhu Pengujian (°C)</Label><Input type="number" v-model.number="form.suhu" /></div>
                                <div class="grid gap-2"><Label>Berat Former (g)</Label><Input type="number" v-model.number="form.berat_former" /></div>
                            </div>
                        </div>

                        <hr class="border-zinc-100 dark:border-zinc-800" />

                        <!-- SECTION 3: PARAMETER FISIK / LAB -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-bold uppercase text-blue-500 flex items-center gap-2">
                                <IconDroplet class="size-4" /> Parameter Kepadatan & Fisik (DWA / Lab)
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="grid gap-1.5"><Label>Thickness (Ketebalan)</Label><Input type="number" step="0.01" v-model.number="form.thickness" /></div>
                                <div class="grid gap-1.5"><Label>Chemical Parameter</Label><Input type="number" step="0.01" v-model.number="form.chemical" /></div>
                                <div class="grid gap-1.5"><Label>Density Material</Label><Input type="number" step="0.01" v-model.number="form.density" /></div>
                                <div class="grid gap-1.5"><Label>Luas Area (cm²)</Label><Input type="number" step="0.01" v-model.number="form.luas_area" /></div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pt-2">
                                <div class="grid gap-1.5"><Label>WA Palm (%)</Label><Input type="number" step="0.001" v-model.number="form.wa_palm" /></div>
                                <div class="grid gap-1.5"><Label>WA MC (%)</Label><Input type="number" step="0.001" v-model.number="form.wa_mc" /></div>
                                <div class="grid gap-1.5"><Label>WA Sli (%)</Label><Input type="number" step="0.001" v-model.number="form.wa_sli" /></div>
                                <div class="grid gap-1.5"><Label>Visual Score / Defect</Label><Input type="number" v-model.number="form.visual" /></div>
                            </div>
                        </div>

                        <!-- BUTTON UPDATE -->
                        <Button type="submit" :disabled="form.processing" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold shadow-md mt-4">
                            <IconLoader2 v-if="form.processing" class="mr-2 animate-spin" />
                            <IconDeviceFloppy v-else class="mr-2" /> Perbarui Hasil Rekapitulasi
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
