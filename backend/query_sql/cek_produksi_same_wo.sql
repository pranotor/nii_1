SELECT * FROM t_produksi_item AS i 
INNER JOIN t_produksi AS p ON p.`id` = i.`prod_id`
WHERE p.wo_id=1461