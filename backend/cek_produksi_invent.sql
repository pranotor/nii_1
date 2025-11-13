SELECT pd.item_id,pd.`kode_perk`, pd.`kode_barang`,pd.`qty`, pd.`prod_id`,p.`prod_no`,i.`item_id`,i.`kode_barang`,i.`qty`,i.`ref_harga`
FROM t_produksi_item AS pd 
INNER JOIN t_produksi AS p ON pd.`prod_id` = p.id
INNER JOIN t_invent AS i ON i.`item_id` = pd.`item_id` AND i.`ref` = p.`prod_no`
WHERE pd.`qty` < 0 AND -1*pd.`qty` <> i.`qty`