SELECT i.item_id,i.ref, s.`sj_no`,q.`so_no`, c.`nama`, c.`nick` 
FROM t_invent AS i 
INNER JOIN t_sj AS s ON s.`sj_no` = i.`ref`
INNER JOIN t_quotation AS q ON q.so_no = s.`so_no`
INNER JOIN t_customer AS c ON c.`id` = q.`cust_id`
WHERE ref LIKE 'SJ%';

UPDATE t_invent AS i 
INNER JOIN t_sj AS s ON s.`sj_no` = i.`ref`
INNER JOIN t_quotation AS q ON q.so_no = s.`so_no`
INNER JOIN t_customer AS c ON c.`id` = q.`cust_id`
SET i.`keterangan` = c.`nick`
WHERE ref LIKE 'SJ%';