export default [{
        url: "/dashboards/classic-dashboard",
        name: "Dashboards",
        icon: "mdi mdi-view-dashboard",
        i18n: "Dashboards",
        index: 1
    },
    {
        url: "/jurnal",
        name: "JURNAL",
        icon: "mdi mdi-credit-card-multiple",
        i18n: "JURNAL",
        index: 2,
        child: [{
                url: "/jurnal/dvud",
                name: "Voucher Utang (DVUD/DHHD)",
                icon: "mdi mdi-cash-multiple",
                i18n: "Voucher Utang (DVUD/DHHD)",
                index: 2.1,
            },
            {

                url: "/jurnal/jbk",
                name: "Pembayaran Kas/Bank (JBK)",
                icon: "mdi mdi-cards-outline",
                i18n: "Pembayaran Kas/Bank (JBK)",
                index: 2.2,
            },
            {

                url: "/jurnal/ju",
                name: "Jurnal Umum (JU)",
                icon: "mdi mdi-bullseye",
                i18n: "Jurnal Umum",
                index: 2.3,
            },
            {

                url: "/jurnal/jra",
                name: "Jurnal Rekening Air",
                icon: "mdi mdi-water",
                i18n: "Jurnal Rekening Air",
                index: 2.4,
            },
            {

                url: "/jurnal/jpk",
                name: "Penerimaan Kas Bank (JPK)",
                icon: "mdi mdi-battery-positive",
                i18n: "Penerimaan Kas Bank (JPK)",
                index: 2.5,
            },
            {

                url: "/jurnal/jpbik",
                name: "Pemakaian Persediaan (JPBIK)",
                icon: "mdi mdi-barcode-scan",
                i18n: "Pemakaian Persediaan (JPBIK)",
                index: 2.6,
            },
            {

                url: "/jurnal/jrna",
                name: "Jurnal Rekening Non Air",
                icon: "mdi mdi-water-off",
                i18n: "Jurnal Rekening Non Air",
                index: 2.7,
            }
        ]
    },
    {
        url: "/aktiva",
        name: "AKTIVA",
        icon: "mdi mdi-store",
        i18n: "AKTIVA",
        index: 3,
        child: [{
                url: "/aktiva/daftar",
                name: "Daftar Aktiva",
                icon: "mdi mdi-store-24-hour",
                i18n: "Daftar Aktiva",
                index: 3.1,
            },
            {

                url: "/component/avatar",
                name: "Hitung Penyusutan",
                icon: "mdi mdi-sort-descending",
                i18n: "Hitung Penyusutan",
                index: 3.2,
            }
        ]
    },
    {
        url: "/inventory",
        name: "INVENTORY",
        icon: "mdi mdi-table",
        i18n: "INVENTORY",
        index: 4,
        child: [{
                url: "/inventory/bpb",
                name: "Tambah Stok (BPB)",
                icon: "mdi mdi-textbox",
                i18n: "Tambah Stok (BPB)",
                index: 4.1,
            },
            {

                url: "/inventory/bpp",
                name: "Permintaan Pemakaian (BPP)",
                icon: "mdi mdi-tag-remove",
                i18n: "Permintaan Pemakaian (BPP)",
                index: 4.2,
            }
        ]
    },
    {
        url: "/inventory",
        name: "POSTING",
        icon: "mdi mdi-vector-selection",
        i18n: "POSTING",
        index: 5,
        child: [{
                url: "/posting/drd",
                name: "POSTING DRD",
                icon: "mdi mdi-texture",
                i18n: "POSTING DRD",
                index: 5.1,
            },
            {

                url: "/posting/lpp",
                name: "POSTING LPP",
                icon: "mdi mdi-transfer",
                i18n: "POSTING LPP",
                index: 5.2,
            },
            {

                url: "/posting/bpb",
                name: "POSTING BPB",
                icon: "mdi mdi-wallet",
                i18n: "POSTING BPB",
                index: 5.3,
            },
            {

                url: "/posting/bpp",
                name: "POSTING PEMAKAIAN (BPP)",
                icon: "mdi mdi-view-day",
                i18n: "POSTING PEMAKAIAN (BPP)",
                index: 5.4,
            }
        ]
    },
    {
        url: "/report/index",
        name: "LAPORAN",
        icon: "mdi mdi-chart-bar",
        i18n: "LAPORAN",
        index: 6
    },
]