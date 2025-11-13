CREATE TABLE wo_blm_produksi AS SELECT d.kode_barang,d.size,h.`so_no`,w.`wo_no`,w.`wo_tgl` FROM t_quotationd AS d
INNER JOIN t_quotation AS h ON d.`qt_id` = h.`id`
INNER JOIN t_wo AS w ON d.`wo_id` = w.`id`
WHERE wo_id <> 0 AND prod_id = 0;