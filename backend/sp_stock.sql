DELIMITER $$

USE `niimockn_db`$$

DROP PROCEDURE IF EXISTS `sp_stock`$$

CREATE DEFINER=`niimockn_usr`@`localhost` PROCEDURE `sp_stock`(tahun INT,bulan INT,periode VARCHAR(10))
BEGIN
	DECLARE no_more_rows BOOLEAN DEFAULT FALSE;
	DECLARE it_id INT;
	DECLARE kp VARCHAR(20);
	DECLARE kb VARCHAR(20);
	DECLARE np VARCHAR(255);
	DECLARE nu VARCHAR(1000);
	DECLARE sat VARCHAR(20);

	DECLARE curTemp CURSOR FOR SELECT item_id,kode_perk,kode_barang,nama_perk,uraian,satuan FROM stock_temp ;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET no_more_rows = TRUE;

	SET @tbl_num := FLOOR(RAND()*(100));
	SET @tbl_temp := CONCAT('stock_temp');
	SET @tbl_fix := CONCAT('stock_fix');
	SET @tbl_final := CONCAT('stock_final',@tbl_num);
	SET @drop_tbl_temp = CONCAT("DROP TEMPORARY TABLE IF EXISTS ",@tbl_temp);
	SET @drop_tbl_fix = CONCAT("DROP TEMPORARY TABLE IF EXISTS ",@tbl_fix);
	SET @drop_tbl_finail = CONCAT("DROP TABLE IF EXISTS ",@tbl_final);
	SET @drop_tbl_copy = CONCAT("DROP TABLE IF EXISTS ",@tbl_final,"_cpy");

	PREPARE stmt FROM @drop_tbl_temp;
	EXECUTE stmt;

	SET @create_tbl_temp = CONCAT("CREATE TEMPORARY TABLE ",@tbl_temp," AS SELECT DISTINCT i.item_id,r.`kode_perk`,
			t.kode_barang,r.`nama_perk`,t.`uraian`,t.`satuan` FROM t_invent AS i
			INNER JOIN t_item AS t ON i.`item_id` = t.`id`
			INNER JOIN rekening AS r ON t.`kode_perk` = r.`kode_perk`
			WHERE i.qty <> i.`used`
			ORDER BY i.kode_barang");

	PREPARE stmt FROM @create_tbl_temp;
	EXECUTE stmt;

	PREPARE stmt FROM @drop_tbl_finail;
	EXECUTE stmt;
	SET @create_tbl_final = CONCAT("CREATE TABLE ",@tbl_final," SELECT * FROM `t_stock_template` WHERE 1 = 0");
	PREPARE stmt FROM @create_tbl_final;
	EXECUTE stmt;

	IF  periode = 'bl' THEN
		IF bulan = 1 THEN
			SET @sql_where_awal = CONCAT(" AND ref='STOCK AWAL' AND YEAR(tgl)=",tahun);
			SET @sql_where_mutasi = CONCAT(" AND ref <> 'STOCK AWAL' AND YEAR(tgl)=",tahun," AND MONTH(tgl)=",bulan);
		ELSE
			SET @sql_where_awal = CONCAT(" AND YEAR(tgl)='",tahun,"' AND MONTH(tgl)<",bulan);
			SET @sql_where_mutasi = CONCAT(" AND YEAR(tgl)='",tahun,"' AND MONTH(tgl)=",bulan);
		END IF;
	ELSEIF periode = 'th' THEN
		SET @sql_where_awal = CONCAT(" AND ref='STOCK AWAL' AND YEAR(tgl)=",tahun);
		SET @sql_where_mutasi = CONCAT(" AND ref <> 'STOCK AWAL' AND YEAR(tgl)=",tahun);
	END IF;
	SET @prev_kode = '';
	SET @prev_np = '';
	OPEN curTemp;
	inventory_loop: LOOP
		FETCH NEXT FROM curTemp INTO it_id, kp, kb, np, nu, sat;
		#select kp;
		IF no_more_rows THEN
			#SET no_more_rows = FALSE;
			SET @nama_perk = CONCAT('JUMLAH ',@prev_np);
			SET @insert_final = CONCAT("INSERT ",@tbl_final," VALUES('",@prev_kode,"','",@nama_perk,"','','JUM',0,0,0,0)");
			PREPARE stmt FROM @insert_final;
			EXECUTE stmt;
			LEAVE inventory_loop;
		END IF;
		SET @cur_kode = kp;
		IF @cur_kode <> @prev_kode THEN
			SET @nama_perk = CONCAT('JUMLAH ',@prev_np);
			IF @prev_kode <> '' THEN
				SET @insert_final = CONCAT("insert ",@tbl_final," VALUES('",@prev_kode,"','",@nama_perk,"','','JUM',0,0,0,0)");
				PREPARE stmt FROM @insert_final;
				EXECUTE stmt;
			END IF;
			SET @nama_perk = CONCAT(kp," - ",np);

			SET @insert_final = CONCAT("insert into ",@tbl_final," values('','",@nama_perk,"','','",kp,"',0,0,0,0)");
			#select @insert_final;
			PREPARE stmt FROM @insert_final;
			EXECUTE stmt;
		END IF;
		#saldo awal
		SET @query_masuk_awal = CONCAT("select IFNULL(sum(qty),0) into @qty_masuk_awal from t_invent where item_id=",it_id," and `status`='IN'",@sql_where_awal);
		SET @query_keluar_awal = CONCAT("select IFNULL(sum(qty),0) into @qty_keluar_awal from t_invent where item_id=",it_id," and `status`='OUT'",@sql_where_awal);
		SET @query_masuk_mut = CONCAT("select IFNULL(sum(qty),0) into @qty_masuk_mut from t_invent where item_id=",it_id," and `status`='IN'",@sql_where_mutasi);
		SET @query_keluar_mut = CONCAT("select IFNULL(sum(qty),0) into @qty_kelual_mut from t_invent where item_id=",it_id," and `status`='OUT'",@sql_where_mutasi);
		#select @query_masuk_awal;
		#SELECT @query_keluar_awal;
		#SELECT @query_masuk_mut;
		#SELECT @query_keluar_mut;

		PREPARE stmt FROM @query_masuk_awal;
		EXECUTE stmt;
		PREPARE stmt FROM @query_keluar_awal;
		EXECUTE stmt;
		PREPARE stmt FROM @query_masuk_mut;
		EXECUTE stmt;
		PREPARE stmt FROM @query_keluar_mut;
		EXECUTE stmt;
		SET @saldo_akhir = @qty_masuk_awal-@qty_keluar_awal+@qty_masuk_mut-@qty_kelual_mut;
		SET @insert_final = CONCAT("insert into ",@tbl_final," values('",kb,"','",nu,"','",sat,"','",kp,"',",@qty_masuk_awal-@qty_keluar_awal,",",@qty_masuk_mut,",",@qty_kelual_mut,",",@saldo_akhir,")");
		PREPARE stmt FROM @insert_final;
		EXECUTE stmt;
		SET @prev_kode = kp;
		SET @prev_np = np;

	END LOOP;
	#copy_table
	PREPARE stmt FROM @drop_tbl_copy;
	EXECUTE stmt;
	SET @create_copy = CONCAT("create table ",@tbl_final,"_cpy as select * from ",@tbl_final);
	PREPARE stmt FROM @create_copy;
	EXECUTE stmt;

	SET @sql_update = CONCAT("update ",@tbl_final," f set awal=(select sum(f1.awal) from ",@tbl_final,"_cpy f1 where f1.perkiraan=f.kode and f1.perkiraan <> 'JUM'),
			  tambah=(select sum(f2.tambah) from ",@tbl_final,"_cpy f2 where f2.perkiraan=f.kode and f2.perkiraan <> 'JUM'),
			  kurang=(select sum(f3.kurang) from ",@tbl_final,"_cpy f3 where f3.perkiraan=f.kode and f3.perkiraan <> 'JUM'),
			  akhir=(select sum(f4.akhir) from ",@tbl_final,"_cpy f4 where f4.perkiraan=f.kode and f4.perkiraan <> 'JUM')
			  where perkiraan='JUM'");
	#select @sql_update;
	PREPARE stmt FROM @sql_update;
	EXECUTE stmt;
	SET @delete_final = CONCAT("DELETE FROM ",@tbl_final," WHERE akhir=0 and kode<> ''");
	PREPARE stmt FROM @delete_final;
	EXECUTE stmt;
	SET @select_final = CONCAT("select * from ",@tbl_final);
	PREPARE stmt FROM @select_final;
	EXECUTE stmt;
	PREPARE stmt FROM @drop_tbl_finail;
	EXECUTE stmt;
	PREPARE stmt FROM @drop_tbl_copy;
	EXECUTE stmt;
    END$$

DELIMITER ;
