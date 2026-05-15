<script setup lang="ts">
import {
    IconInnerShadowTop,
    IconPackage,
    IconFileDescription,
    IconClipboardList,
    IconSettingsAutomation,
    IconUsers,
    IconHierarchy,
    IconBuilding,
    IconGitMerge,
    TugasProduksi,
    IconUserPlus,
} from "@tabler/icons-vue";

import Master from "@/components/Master.vue";
import NavMain from "@/components/NavMain.vue";
import NavUser from "@/components/NavUser.vue";
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from "@/components/ui/sidebar";
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

// Ambil data auth dari shared props (HandleInertiaRequests.php)
const page = usePage();
const user = computed(() => page.props.auth.user);
const userRoles = computed(() => (page.props.auth as any).roles as string[]);

/**
 * Logika Hak Akses:
 * isAdminOrQC untuk menu Sampel, Formulir, dan Master Data
 */
const isAdminOrQC = computed(() => {
    return userRoles.value.includes('admin') || userRoles.value.includes('Quality Control');
});

/**
 * Logika Hak Akses: Tugas Produksi
 * Hanya bisa dilihat oleh admin, operator, manager, dan supervisor
 */
const canAccessTugasProduksi = computed(() => {
    const allowed = ['admin', 'Operator', 'Leader', 'Manager', 'Supervisor'];
    return userRoles.value.some(role => allowed.includes(role.toLowerCase()) || allowed.includes(role));
});

/**
 * Logika Hak Akses: Persetujuan Manager
 * Hanya bisa dibuka oleh admin, QC Manager, dan Factory Manager
 */
const canAccessManagerApproval = computed(() => {
    const allowed = ['admin', 'QC Manager', 'Factory Manager'];
    return userRoles.value.some(role => allowed.includes(role));
});

/**
 * Menu Navigasi Utama
 */
const filteredNavMain = computed(() => {
    let menus: any[] = [];

    menus.push({
        title: "Daftar Pengguna",
        url: route("daftar.pengguna.index"),
        icon: IconUsers,
        root: "DaftarPengguna",
    });

    return menus;
});

// Data untuk menu Master
const masterData = [
    {
        name: "Pengguna",
        url: route("users.index"),
        icon: IconUsers,
        root: "Master/Users",
    },
    {
        name: "Jabatan",
        url: route("roles.index"),
        icon: IconHierarchy,
        root: "Master/Roles",
    },
    {
        name: "Customer",
        url: route("customer.index"),
        icon: IconUserPlus,
        root: "Master/Customer",
    },
];
</script>

<template>
    <Sidebar collapsible="offcanvas">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton
                        as-child
                        class="data-[slot=sidebar-menu-button]:!p-1.5"
                    >
                        <Link
                            :href="route('dashboard')"
                            as="button"
                            class="w-full text-left"
                        >
                            <IconInnerShadowTop class="!size-5" />
                            <span class="font-bold tracking-tight">Sitarman</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="filteredNavMain" />
            <Master v-if="isAdminOrQC" :items="masterData" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser :user="user" />
        </SidebarFooter>
    </Sidebar>
</template>
