import Vue from 'vue'
import Router from 'vue-router'
import store from './store/store'

Vue.use(Router)

const router = new Router({
    mode: 'history',
    scrollBehavior() {
        return {
            x: 0,
            y: 0
        }
    },
    base: process.env.BASE_URL,
    routes: [
        // ======================
        // Blank Layout
        // ======================    
        {
            path: '',
            component: () => import('./layout/blank/Blank.vue'),
            // ======================
            // Theme routes / pages
            // ======================
            children: [{
                path: '/',
                redirect: '/login',
                meta: { requiresVisitor: true }
            }, {
                path: '/login',
                name: 'Login',
                index: 1,
                component: () => import('./views/authentication/Login.vue'),
                meta: { requiresVisitor: true }
            }, {
                path: '/error404',
                name: 'Error 404',
                index: 2,
                component: () => import('./views/authentication/Error404.vue')
            }]
        },
        {
            // ======================
            // Full Layout
            // ======================
            path: '',
            component: () => import('./layout/full/MainContainer.vue'),
            // ======================
            // Theme routes / pages
            // ======================
            children: [{
                path: '/dashboards/classic-dashboard',
                name: 'Dashboard',
                index: 1,
                meta: {
                    breadcrumb: [{
                        title: 'Dashboards',
                        url: '/dashboards/classic-dashboard'
                    },
                    {
                        title: 'Classic Dashboard',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/dashboards/classic-dashboard/ClassicDashboard.vue')
            },{
                path: '/master/customer',
                name: 'customerlist',
                index: 1,
                meta: {
                    breadcrumb: [{
                        title: 'Dashboards',
                        url: '/master/customer'
                    },
                    {
                        title: 'Master Customer',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/master/customer/List.vue')
            },{
                path: '/master/customer/input',
                name: 'customerinput',
                index: 1,
                meta: {
                    breadcrumb: [{
                        title: 'Dashboards',
                        url: '/master/customer'
                    },
                    {
                        title: 'Master Customer',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/master/customer/Input.vue')
            },{
                path: '/crm/customer/edit/:ref',
                name: 'customeredit',
                index: 1,
                meta: {
                    breadcrumb: [{
                        title: 'Dashboards',
                        url: '/master/customer'
                    },
                    {
                        title: 'Classic Dashboard',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/master/customer/Input.vue')
            },{
                path: '/crm/quotation',
                name: 'quotation',
                index: 1,
                meta: {
                    breadcrumb: [{
                        title: 'Dashboards',
                        url: '/master/customer'
                    },
                    {
                        title: 'Classic Dashboard',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/crm/quotation/List.vue')
            },{
                path: '/crm/quotationinput',
                name: 'quotationinput',
                index: 1,
                meta: {
                    breadcrumb: [{
                        title: 'Dashboards',
                        url: '/master/customer'
                    },
                    {
                        title: 'Classic Dashboard',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/crm/quotation/Input.vue')
            },{
                path: '/crm/quotationinput/edit/:ref',
                name: 'quotationedit',
                index: 1,
                meta: {
                    breadcrumb: [{
                        title: 'Dashboards',
                        url: '/master/customer'
                    },
                    {
                        title: 'Classic Dashboard',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/crm/quotation/Input.vue')
            },{
                path: '/crm/so',
                name: 'so',
                index: 1,
                meta: {
                    breadcrumb: [{
                        title: 'Dashboards',
                        url: '/master/customer'
                    },
                    {
                        title: 'Classic Dashboard',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/crm/so/List.vue')
            },{
                path: '/crm/soinput',
                name: 'soinput',
                index: 1,
                meta: {
                    breadcrumb: [{
                        title: 'Dashboards',
                        url: '/master/customer'
                    },
                    {
                        title: 'Classic Dashboard',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/crm/so/Input.vue')
            },{
                path: '/crm/soinput/edit/:ref',
                name: 'soedit',
                index: 1,
                meta: {
                    breadcrumb: [{
                        title: 'Dashboards',
                        url: '/master/customer'
                    },
                    {
                        title: 'Classic Dashboard',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/crm/so/Input.vue')
            },{
                path: '/crm/sr',
                name: 'sr',
                index: 1,
                meta: {
                    breadcrumb: [{
                        title: 'Dashboards',
                        url: '/master/customer'
                    },
                    {
                        title: 'Classic Dashboard',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/crm/sr/List.vue')
            },{
                path: '/crm/srinput',
                name: 'srinput',
                index: 1,
                meta: {
                    breadcrumb: [{
                        title: 'Dashboards',
                        url: '/master/customer'
                    },
                    {
                        title: 'Classic Dashboard',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/crm/sr/Input.vue')
            },{
                path: '/crm/srinput/edit/:ref',
                name: 'sredit',
                index: 1,
                meta: {
                    breadcrumb: [{
                        title: 'Dashboards',
                        url: '/master/customer'
                    },
                    {
                        title: 'Classic Dashboard',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/crm/sr/Input.vue')
            },{
                path: '/crm/wo',
                name: 'wo',
                index: 1,
                meta: {
                    breadcrumb: [{
                        title: 'Dashboards',
                        url: '/master/customer'
                    },
                    {
                        title: 'Classic Dashboard',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/crm/wo/List.vue')
            },{
                path: '/crm/woinput',
                name: 'woinput',
                index: 1,
                meta: {
                    breadcrumb: [{
                        title: 'Dashboards',
                        url: '/master/customer'
                    },
                    {
                        title: 'Classic Dashboard',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/crm/wo/Input.vue')
            },{
                path: '/crm/produksi',
                name: 'produksi',
                index: 1,
                meta: {
                    breadcrumb: [{
                        title: 'Dashboards',
                        url: '/master/customer'
                    },
                    {
                        title: 'Classic Dashboard',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/crm/produksi/List.vue')
            },{
                path: '/crm/produksiinput',
                name: 'produksiinput',
                index: 1,
                meta: {
                    breadcrumb: [{
                        title: 'Dashboards',
                        url: '/master/customer'
                    },
                    {
                        title: 'Classic Dashboard',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/crm/produksi/Input.vue')
            },{
                path: '/crm/produksiinput/:ref',
                name: 'produksiedit',
                index: 1,
                meta: {
                    breadcrumb: [{
                        title: 'Dashboards',
                        url: '/master/customer'
                    },
                    {
                        title: 'Classic Dashboard',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/crm/produksi/Edit.vue')
            },{
                path: '/crm/cutting',
                name: 'cutting',
                index: 1,
                meta: {
                    breadcrumb: [{
                        title: 'Dashboards',
                        url: '/master/customer'
                    },
                    {
                        title: 'Classic Dashboard',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/crm/cutting/List.vue')
            },{
                path: '/crm/cuttinginput',
                name: 'cuttinginput',
                index: 1,
                meta: {
                    breadcrumb: [{
                        title: 'Dashboards',
                        url: '/master/customer'
                    },
                    {
                        title: 'Classic Dashboard',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/crm/cutting/Input.vue')
            },{
                path: '/crm/cuttinginput/:ref',
                name: 'cuttingedit',
                index: 1,
                meta: {
                    breadcrumb: [{
                        title: 'Dashboards',
                        url: '/master/customer'
                    },
                    {
                        title: 'Classic Dashboard',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/crm/cutting/Input.vue')
            },{
                path: '/master/tandatangan',
                name: 'ttdlist',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Tandatangan',
                        url: '/master/tandatangan'
                    },
                    {
                        title: 'Tanda Tangan',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/master/tandatangan/List.vue')
            },{
                path: '/master/tandatangan/input',
                name: 'ttdinput',
                index: 3,
                meta: {
                    breadcrumb: [{
                        title: 'Tandatangan',
                        url: '/master/tandatangan'
                    },
                    {
                        title: 'Tanda Tangan',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/master/tandatangan/Input.vue')
            },{
                path: '/master/tandatangan/edit/:ref',
                name: 'ttdedit',
                index: 4,
                meta: {
                    breadcrumb: [{
                        title: 'Tandatangan',
                        url: '/master/tandatangan/edit/:ref'
                    },
                    {
                        title: 'Tanda Tangan',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/master/tandatangan/Input.vue')
            },{
                path: '/master/rekanan',
                name: 'rekananlist',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Rekanan',
                        url: '/master/rekanan'
                    },
                    {
                        title: 'Rekanan',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/master/rekanan/List.vue')
            },{
                path: '/master/rekanan/input',
                name: 'rekananinput',
                index: 3,
                meta: {
                    breadcrumb: [{
                        title: 'Rekanan',
                        url: '/master/rekanan'
                    },
                    {
                        title: 'Rekanan',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/master/rekanan/Input.vue')
            },{
                path: '/master/rekanan/edit/:ref',
                name: 'rekananedit',
                index: 4,
                meta: {
                    breadcrumb: [{
                        title: 'Rekanan',
                        url: '/master/rekanan/edit/:ref'
                    },
                    {
                        title: 'Rekanan',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/master/rekanan/Input.vue')
            },{
                path: '/master/inventory',
                name: 'inventorylist',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Inventory',
                        url: '/master/inventory'
                    },
                    {
                        title: 'Inventory',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/master/inventory/List.vue')
            },{
                path: '/master/inventory/input',
                name: 'inventoryinput',
                index: 3,
                meta: {
                    breadcrumb: [{
                        title: 'Inventory',
                        url: '/master/inventory'
                    },
                    {
                        title: 'Inventory',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/master/inventory/Input.vue')
            },{
                path: '/master/inventory/edit/:ref',
                name: 'inventoryedit',
                index: 4,
                meta: {
                    breadcrumb: [{
                        title: 'Inventory',
                        url: '/master/inventory/edit/:ref'
                    },
                    {
                        title: 'Inventory',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/master/inventory/Input.vue')
            },{
                path: '/master/pegawai',
                name: 'pegawailist',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Pegawai',
                        url: '/master/pegawai'
                    },
                    {
                        title: 'Pegawai',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/master/pegawai/List.vue')
            },{
                path: '/master/pegawai/input',
                name: 'pegawaiinput',
                index: 3,
                meta: {
                    breadcrumb: [{
                        title: 'Pegawai',
                        url: '/master/pegawai'
                    },
                    {
                        title: 'Pegawai',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/master/pegawai/Input.vue')
            },{
                path: '/master/pegawai/edit/:ref',
                name: 'pegawaiedit',
                index: 4,
                meta: {
                    breadcrumb: [{
                        title: 'Pegawai',
                        url: '/master/pegawai/edit/:ref'
                    },
                    {
                        title: 'Inventory',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/master/pegawai/Input.vue')
            },{
                path: '/master/perkiraan',
                name: 'perkiraanlist',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Perkiraan',
                        url: '/master/perkiraan'
                    },
                    {
                        title: 'Perkiraan',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/master/perkiraan/List.vue')
            },{
                path: '/master/perkiraan/input',
                name: 'perkiraaninput',
                index: 3,
                meta: {
                    breadcrumb: [{
                        title: 'Perkiraan',
                        url: '/master/perkiraan'
                    },
                    {
                        title: 'Perkiraan',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/master/perkiraan/Input.vue')
            },{
                path: '/master/perkiraan/edit/:ref',
                name: 'perkiraanedit',
                index: 4,
                meta: {
                    breadcrumb: [{
                        title: 'Perkiraan',
                        url: '/master/perkiraan/edit/:ref'
                    },
                    {
                        title: 'Perkiraan',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/master/perkiraan/Input.vue')
            },{
                path: '/master/role',
                name: 'rolelist',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Role',
                        url: '/master/role'
                    },
                    {
                        title: 'Role',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/master/role/List.vue')
            },{
                path: '/master/role/input',
                name: 'roleinput',
                index: 3,
                meta: {
                    breadcrumb: [{
                        title: 'Role',
                        url: '/master/role'
                    },
                    {
                        title: 'Role',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/master/role/Input.vue')
            },{
                path: '/master/role/edit/:ref',
                name: 'roleedit',
                index: 4,
                meta: {
                    breadcrumb: [{
                        title: 'Role',
                        url: '/master/role/edit/:ref'
                    },
                    {
                        title: 'Role',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/master/role/Input.vue')
            },{
                path: '/master/user',
                name: 'userlist',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'User',
                        url: '/master/user'
                    },
                    {
                        title: 'User',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/master/user/List.vue')
            },{
                path: '/master/user/input',
                name: 'userinput',
                index: 3,
                meta: {
                    breadcrumb: [{
                        title: 'User',
                        url: '/master/user'
                    },
                    {
                        title: 'User',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/master/user/Input.vue')
            },{
                path: '/master/user/:ref',
                name: 'useredit',
                index: 4,
                meta: {
                    breadcrumb: [{
                        title: 'Role',
                        url: '/master/user/edit/:ref'
                    },
                    {
                        title: 'User',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/master/user/Input.vue')
            },{
                path: '/keuangan/invoice',
                name: 'invoicelist',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'JRA',
                        url: '/jurnal/jra'
                    },
                    {
                        title: 'Jurnal Rekening Air',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/keuangan/invoicing/List.vue')
            },
            {
                path: '/keuangan/invoice/edit/:ref',
                name: 'invoiceedit',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'JRA',
                        url: '/jurnal/jra'
                    },
                    {
                        title: 'Jurnal Rekening Air',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/keuangan/invoicing/Input.vue')
            },
            {
                path: '/keuangan/invoicebank/edit/:ref',
                name: 'invoicebankedit',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'JRA',
                        url: '/jurnal/jra'
                    },
                    {
                        title: 'Jurnal Rekening Air',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/keuangan/invoicing/BankInput.vue')
            },
            {
                path: '/keuangan/retur',
                name: 'returindex',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'JRA',
                        url: '/jurnal/jra'
                    },
                    {
                        title: 'Jurnal Rekening Air',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/keuangan/retur/List.vue')
            },
            {
                path: '/keuangan/returlist',
                name: 'returlist',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'JRA',
                        url: '/jurnal/jra'
                    },
                    {
                        title: 'Jurnal Rekening Air',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/keuangan/retur/ReturList.vue')
            },
            {
                path: '/keuangan/retur/input/:ref',
                name: 'returinput',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'JRA',
                        url: '/jurnal/jra'
                    },
                    {
                        title: 'Jurnal Rekening Air',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/keuangan/retur/Input.vue')
            },
            {
                path: '/keuangan/piutang',
                name: 'piutanglist',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'JRA',
                        url: '/jurnal/jra'
                    },
                    {
                        title: 'Jurnal Rekening Air',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/keuangan/piutang/List.vue')
            },
            {
                path: '/keuangan/piutang/input',
                name: 'piutanginput',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'JRA',
                        url: '/jurnal/jra'
                    },
                    {
                        title: 'Jurnal Rekening Air',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/keuangan/piutang/Input.vue')
            },
            {
                path: '/keuangan/piutang/edit/:ref',
                name: 'piutangedit',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'JRA',
                        url: '/jurnal/jra'
                    },
                    {
                        title: 'Jurnal Rekening Air',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/keuangan/piutang/Input.vue')
            },
            {
                path: '/keuangan/ttinvoice',
                name: 'ttinvoicelist',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'JRA',
                        url: '/jurnal/jra'
                    },
                    {
                        title: 'Jurnal Rekening Air',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/keuangan/ttinvoice/List.vue')
            },
            {
                path: '/keuangan/ttinvoice/input',
                name: 'ttinvoiceinput',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'JRA',
                        url: '/jurnal/jra'
                    },
                    {
                        title: 'Jurnal Rekening Air',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/keuangan/ttinvoice/Input.vue')
            },
            {
                path: '/keuangan/bankin',
                name: 'bankinlist',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'JRA',
                        url: '/jurnal/jra'
                    },
                    {
                        title: 'Jurnal Rekening Air',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/keuangan/bank/List.vue')
            },{
                path: '/keuangan/bankin/input',
                name: 'bankininput',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'JRA',
                        url: '/jurnal/jra'
                    },
                    {
                        title: 'Jurnal Rekening Air',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/keuangan/bank/Input.vue')
            },
            {
                path: '/keuangan/bankin/edit/:ref',
                name: 'bankinedit',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'JRA',
                        url: '/jurnal/jra'
                    },
                    {
                        title: 'Jurnal Rekening Air',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/keuangan/bank/Edit.vue')
            },
            {
                path: '/keuangan/blokir',
                name: 'blokir',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'JRA',
                        url: '/jurnal/jra'
                    },
                    {
                        title: 'Jurnal Rekening Air',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/keuangan/blokir/List.vue')
            },
            {
                path: '/keuangan/blokir/input',
                name: 'blokirinput',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'JRA',
                        url: '/jurnal/jra'
                    },
                    {
                        title: 'Jurnal Rekening Air',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/keuangan/blokir/Input.vue')
            },
            {
                path: '/keuangan/nomorfp',
                name: 'nofplist',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'JRA',
                        url: '/jurnal/jra'
                    },
                    {
                        title: 'Jurnal Rekening Air',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/keuangan/nomorfp/List.vue')
            },
            {
                path: '/keuangan/nomorfp/input',
                name: 'nofpinput',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'JRA',
                        url: '/jurnal/jra'
                    },
                    {
                        title: 'Jurnal Rekening Air',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/keuangan/nomorfp/Input.vue')
            },
            {
                path: '/keuangan/convertFp',
                name: 'convertFp',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'JRA',
                        url: '/jurnal/jra'
                    },
                    {
                        title: 'Jurnal Rekening Air',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/keuangan/nomorfp/ConvertFp.vue')
            },
            {
                path: '/persetujuan/unblokir',
                name: 'unblokir',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'JRA',
                        url: '/jurnal/jra'
                    },
                    {
                        title: 'Jurnal Rekening Air',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/persetujuan/unblokir/List.vue')
            },
            {
                path: '/inventory/po',
                name: 'polist',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Tambah stok (BPB)',
                        url: '/inventory/bpb'
                    },
                    {
                        title: 'Tambah stok (BPB)',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/inventory/po/Index.vue')
            },{
                path: '/inventory/po/input',
                name: 'poinput',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Tambah stok (BPB)',
                        url: '/inventory/bpb'
                    },
                    {
                        title: 'Tambah stok (BPB)',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/inventory/po/Input.vue')
            },{
                path: '/inventory/po/edit/:ref',
                name: 'poedit',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Tambah stok (BPB)',
                        url: '/inventory/bpb/edit/:ref'
                    },
                    {
                        title: 'Tambah stok (BPB)',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/inventory/po/Input.vue')
            },{
                path: '/inventory/bpb',
                name: 'bpblist',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Tambah stok (BPB)',
                        url: '/inventory/bpb'
                    },
                    {
                        title: 'Tambah stok (BPB)',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/inventory/bpb/Index.vue')
            },{
                path: '/inventory/retur-beli',
                name: 'returbelilist',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Inventory',
                        url: '/inventory/retur-beli'
                    },
                    {
                        title: 'Retur Pembelian',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/inventory/retur_beli/Index.vue')
            },{
                path: '/inventory/retur-beli/input/:id',
                name: 'returbeliinput',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Inventory',
                        url: '/inventory/retur-beli'
                    },
                    {
                        title: 'Input Retur Pembelian',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/inventory/retur_beli/Input.vue')
            },{
                path: '/inventory/sj',
                name: 'sjlist',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Tambah stok (BPB)',
                        url: '/inventory/bpb'
                    },
                    {
                        title: 'Tambah stok (BPB)',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/inventory/sj/List.vue')
            },{
                path: '/inventory/sjlist',
                name: 'sjcreated',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Tambah stok (BPB)',
                        url: '/inventory/bpb'
                    },
                    {
                        title: 'Tambah stok (BPB)',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/inventory/sj/ListSj.vue')
            },{
                path: '/inventory/sj/input/:ref',
                name: 'sjinput',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Tambah stok (BPB)',
                        url: '/inventory/bpb/edit/:ref'
                    },
                    {
                        title: 'Tambah stok (BPB)',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/inventory/sj/Form.vue')
            },{
                path: '/inventory/sj/edit/:ref',
                name: 'sjedit',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Tambah stok (BPB)',
                        url: '/inventory/bpb/edit/:ref'
                    },
                    {
                        title: 'Tambah stok (BPB)',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/inventory/sj/FormEdit.vue')
            },{
                path: '/inventory/sr',
                name: 'sjsrlist',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Tambah stok (BPB)',
                        url: '/inventory/bpb'
                    },
                    {
                        title: 'Tambah stok (BPB)',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/inventory/sr/List.vue')
            },{
                path: '/inventory/srlist',
                name: 'sjsrcreated',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Tambah stok (BPB)',
                        url: '/inventory/bpb'
                    },
                    {
                        title: 'Tambah stok (BPB)',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/inventory/sr/ListSj.vue')
            },{
                path: '/inventory/sr/input/:ref',
                name: 'sjsrinput',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Tambah stok (BPB)',
                        url: '/inventory/bpb/edit/:ref'
                    },
                    {
                        title: 'Tambah stok (BPB)',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/inventory/sr/Form.vue')
            },{
                path: '/inventory/sr/edit/:ref',
                name: 'sjsredit',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Tambah stok (BPB)',
                        url: '/inventory/bpb/edit/:ref'
                    },
                    {
                        title: 'Tambah stok (BPB)',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/inventory/sr/FormEdit.vue')
            },{
                path: '/inventory/bpb/input',
                name: 'bpbinput',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Tambah stok (BPB)',
                        url: '/inventory/bpb'
                    },
                    {
                        title: 'Tambah stok (BPB)',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/inventory/bpb/Input.vue')
            },{
                path: '/inventory/bpb/edit/:ref',
                name: 'bpbedit',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Tambah stok (BPB)',
                        url: '/inventory/bpb/edit/:ref'
                    },
                    {
                        title: 'Tambah stok (BPB)',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/inventory/bpb/Edit.vue')
            },{
                path: '/inventory/hitung',
                name: 'hitungstok',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Tambah stok (BPB)',
                        url: '/inventory/bpb/edit/:ref'
                    },
                    {
                        title: 'Tambah stok (BPB)',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/inventory/Hitungstok.vue')
            },{
                path: '/inventory/saldoAwal',
                name: 'hitungsaldoawal',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Tambah stok (BPB)',
                        url: '/inventory/bpb/edit/:ref'
                    },
                    {
                        title: 'Tambah stok (BPB)',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/inventory/Hitungsaldoawal.vue')
            },
            {
                path: '/posting/bpb',
                name: 'postingbpblist',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Tambah stok (BPB)',
                        url: '/inventory/bpb'
                    },
                    {
                        title: 'Tambah stok (BPB)',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/inventory/bpb/PostingIndex.vue')
            },{
                path: '/posting/bpb/edit/:ref',
                name: 'postingbpbedit',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Posting Tambah stok (BPB)',
                        url: '/inventory/bpb/edit/:ref'
                    },
                    {
                        title: 'Posting Tambah stok (BPB)',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/inventory/bpb/PostingInput.vue')
            },
            {
                path: '/inventory/bpp',
                name: 'bpplist',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Perminataan Pemakaian Barang (BPP)',
                        url: '/inventory/bpp'
                    },
                    {
                        title: 'Perminataan Pemakaian Barang (BPP)',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/inventory/bpp/Index.vue')
            },{
                path: '/inventory/bpp/input',
                name: 'bppinput',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Perminataan Pemakaian Barang (BPP)',
                        url: '/inventory/bpb'
                    },
                    {
                        title: 'Perminataan Pemakaian Barang (BPP)',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/inventory/bpp/Input.vue')
            },{
                path: '/inventory/bpp/edit/:ref',
                name: 'bppedit',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Perminataan Pemakaian Barang (BPP)',
                        url: '/inventory/bpp/edit/:ref'
                    },
                    {
                        title: 'Perminataan Pemakaian Barang (BPP)',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/inventory/bpp/Input.vue')
            },

            {
                path: '/posting/bpp',
                name: 'postingbpplist',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'POSTING PEMAKAIAN (BPP)',
                        url: '/posting/bpp'
                    },
                    {
                        title: 'POSTING PEMAKAIAN (BPP)',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/inventory/bpp/PostingIndex.vue')
            },{
                path: '/posting/bpp/edit/:ref',
                name: 'postingbppedit',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'POSTING PEMAKAIAN (BPP)',
                        url: '/inventory/bpb/edit/:ref'
                    },
                    {
                        title: 'POSTING PEMAKAIAN (BPP)',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/inventory/bpp/PostingInput.vue')
            },
            {
                path: '/jurnal/ju',
                name: 'julist',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'POSTING PEMAKAIAN (BPP)',
                        url: '/inventory/bpb/edit/:ref'
                    },
                    {
                        title: 'POSTING PEMAKAIAN (BPP)',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/jurnal/ju/Index.vue')
            },
            {
                path: '/jurnal/ju/input',
                name: 'juinput',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'POSTING PEMAKAIAN (BPP)',
                        url: '/inventory/bpb/edit/:ref'
                    },
                    {
                        title: 'POSTING PEMAKAIAN (BPP)',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/jurnal/ju/Input.vue')
            },
            {
                path: '/jurnal/ju/input/:ref',
                name: 'juedit',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'POSTING PEMAKAIAN (BPP)',
                        url: '/inventory/bpb/edit/:ref'
                    },
                    {
                        title: 'POSTING PEMAKAIAN (BPP)',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/jurnal/ju/Input.vue')
            },
            
            {
                path: '/report/index',
                name: 'report',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Laporan',
                        url: '/report/index'
                    },
                    {
                        title: 'Laporan',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/report/Index.vue')
            },
            {
                path: '/closing/tutup-bulan',
                name: 'tutupbulan',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Tutup Bulan',
                        url: '/closing/tutup-bulan'
                    },
                    {
                        title: 'Tutup Bulan',
                        active: true
                    },
                    ],
                    requiresAuth: true
                },
                component: () => import('./views/closing/TutupBulan.vue')
            },
            {
                path: '/akuntansi/posting-penjualan',
                name: 'postingpenjualan',
                index: 2,
                meta: {
                    breadcrumb: [{
                        title: 'Akuntansi',
                        url: '/akuntansi/posting-penjualan'
                    },
                    {
                        title: 'Posting Penjualan',
                        active: true
                    }],
                    requiresAuth: true
                },
                component: () => import('./views/akuntansi/PostingPenjualan.vue')
            },       
            ]
        },
        // Redirect to 404 page, if no match found
        {
            path: '*',
            redirect: '/Error404'
        }
    ]
})

import NProgress from 'nprogress';

function isloggedIn(){
    return store.getters.getLoggedin;
}

/*router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        // this route requires auth, check if logged in
        // if not, redirect to login page.
        if (!isloggedIn()) {
        next({
            name: 'Login'
        })
        } else {
        next()
        }
    } else if (to.matched.some(record => record.meta.requiresVisitor)) {
        // this route requires auth, check if logged in
        // if not, redirect to login page.
        if (isloggedIn()) {
        next({
            name: 'Dashboard'
        })
        } else {
        next()
        }
    } else {
        next() // make sure to always call next()!
    }
})*/

router.beforeResolve((to, from, next) => {
    // If this isn't an initial page load.
    if (to.name) {
        // Start the route progress bar.
        NProgress.start()
    }
    if (to.matched.some(record => record.meta.requiresAuth)) {
        // this route requires auth, check if logged in
        // if not, redirect to login page.
        if (!isloggedIn()) {
        next({
            name: 'Login'
        })
        } else {
        next()
        }
    } else if (to.matched.some(record => record.meta.requiresVisitor)) {
        // this route requires auth, check if logged in
        // if not, redirect to login page.
        if (isloggedIn()) {
        next({
            name: 'Dashboard'
        })
        } else {
        next()
        }
    } 
    next()
})

router.afterEach(() => {
    // Complete the animation of the route progress bar.
    NProgress.done()
})
export default router