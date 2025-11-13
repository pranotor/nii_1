SELECT w.id,wo_no,qt_id,wo_tgl,(SELECT COUNT(id) FROM t_quotationd AS qd WHERE qd.qt_id = w.`qt_id`) AS item_quot,(SELECT COUNT(id) FROM t_produksi AS i WHERE i.wo_id=w.id) AS produksi FROM t_wo AS w WHERE id NOT IN (SELECT wo_id FROM t_quotationd)

DELETE FROM t_wo WHERE id NOT IN (SELECT wo_id FROM t_quotationd) AND MONTH(wo_tgl) < 12;