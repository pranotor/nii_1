SELECT i.*,w.id AS wo_id,w.`wo_no`,w.`qt_id`,w.`wo_tgl` FROM t_produksi_item i INNER JOIN t_produksi p ON i.`prod_id` = p.id 
INNER JOIN t_wo w ON w.`id` = p.`wo_id`
WHERE qd_id NOT IN (SELECT id FROM t_quotationd) AND i.qty > 0;