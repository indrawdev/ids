<?php

class MBeli extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function cekreg($sRangka,$sMesin)
	{
		$xSQL = ("
			SELECT	TOP 1 a.fs_rangka, a.fs_machine, ISNULL(b.fs_nm_product, '') fs_nm_product
			FROM 	tm_icregister a (NOLOCK)
			LEFT JOIN tm_product b (NOLOCK) ON a.fs_kd_product = b.fs_kd_product
		");
            
		if (trim($sRangka) <> '')
		{
			$xSQL = $xSQL.("
				WHERE 	a.fs_rangka = '".trim($sRangka)."'
			");
		}
		
		if (trim($sMesin) <> '')
		{
			$xSQL = $xSQL.("
				WHERE 	a.fs_machine = '".trim($sMesin)."'
			");
		}
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function grid_prod($sRefno)
	{
		$xSQL = ("
			SELECT	a.fs_kd_product, a.fs_nm_product, a.fn_qtytr fn_qty,
					a.fs_unitbill fs_kd_unit, ISNULL(c.fs_nm_unit, '') fs_nm_unit,
					a.fn_unitprc fn_harga, a.fn_dscprc fn_diskon, a.fs_luxcd fs_kd_lux,
					a.fs_luxnm fs_nm_lux, a.fn_luxpct fn_persen, a.fn_luxamt fn_lux,
					a.fs_seqno
			FROM	tx_posdetail a (NOLOCK)
			LEFT JOIN tx_unitproduct b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_kd_product = b.fs_kd_product
			LEFT JOIN tm_unitconvertion c (NOLOCK) ON a.fs_kd_comp = c.fs_kd_comp
				AND b.fs_kd_unit = c.fs_kd_unit
				AND c.fb_active = 1
			WHERE	a.fs_refno = '".trim($sRefno)."'
			ORDER BY a.fs_seqno
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function grid_reg($sRefno)
	{
		$xSQL = ("
			SELECT	a.fs_rangka, a.fs_machine fs_mesin, a.fn_silinder fs_cc,
					a.fd_thnpembuatan fs_thn, a.fs_kd_warna fs_kd_color, ISNULL(b.fs_nm_vareable, '') fs_nm_color,
					a.fs_kd_wh, ISNULL(c.fs_nm_code, '') fs_nm_wh, a.fn_hpp,
					a.fs_seqno, a.fs_kd_product
			FROM	tm_icregister a (NOLOCK)
			LEFT JOIN tm_vareable b (NOLOCK) ON a.fs_kd_warna = b.fs_kd_vareable
				AND b.fs_key = '08'
			LEFT JOIN tm_addr c (NOLOCK) ON a.fs_kd_wh = LTRIM(RTRIM(c.fs_code)) + LTRIM(RTRIM(c.fs_count))
				AND c.fs_cdtyp = '11'
			WHERE	a.fs_refno = '".trim($sRefno)."'
			ORDER BY   a.fs_seqno, a.fs_seqnoRegister
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cek_refno($sTrx,$sRefno)
	{
		$xSQL = ("
			SELECT	TOP 1 fs_refno
			FROM 	tx_posheader (NOLOCK)
			WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND fs_count = '".trim($this->session->userdata('gCount'))."'
				AND fs_kd_trx = '".trim($sTrx)."'
				AND fs_refno = '".trim($sRefno)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cek_jual($sRangka,$sMesin)
	{
		$xSQL = ("
			SELECT	a.fs_kd_product, a.fs_rangka, a.fs_machine
			FROM   	tm_icregister a (NOLOCK)
			WHERE  	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	a.fs_rangka IN (".trim($sRangka).")
				AND	a.fs_machine IN (".trim($sMesin).")
				AND	EXISTS (
					SELECT	*
					FROM	tm_icregister b
					WHERE	b.fs_rangka = a.fs_rangka
						AND	b.fs_machine = a.fs_machine
						AND	LTRIM(RTRIM(b.fs_refnoinv)) <> ''
				)
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cek_mutasi($sRangka,$sMesin)
	{
		$xSQL = ("
			SELECT	a.fs_kd_product, a.fs_rangka, a.fs_machine
			FROM   	tm_icregister a (NOLOCK)
			WHERE  	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	a.fs_rangka IN (".trim($sRangka).")
				AND	a.fs_machine IN (".trim($sMesin).")
				AND	EXISTS (
					SELECT	*
					FROM	tm_icregister b
					WHERE	b.fs_rangka = a.fs_rangka
						AND	b.fs_machine = a.fs_machine
						AND	LTRIM(RTRIM(b.fs_kd_trxictout)) = '3400'
				)
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function reg_last($sRefno,$sRangka,$sMesin)
	{
		$xSQL = ("
			UPDATE	tm_icregister
				SET	fs_kd_ldept = '".trim($this->session->userdata('gDept'))."',
					fs_countdept = '".trim($this->session->userdata('gCount'))."',
                    fs_kd_trxl = 'BL',
					fs_kd_strxl = '0100',
					fs_refnol = '".trim($sRefno)."',
					fs_seqnol = '000001'
			WHERE	fs_rangka = '".trim($sRangka)."'
				AND	fs_machine = '".trim($sMesin)."'
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function pos_beli($sRefno,$sTrx,$sSalesMtdBeli)
	{
		$xSQL = ("
			EXEC stp_posbeli '".trim($this->session->userdata('gComp'))."','".trim($this->session->userdata('gDept'))."',
                    '".trim($this->session->userdata('gCount'))."','".trim($sTrx)."',' ','".trim($sRefno)."'
					
			EXEC stp_stock_update '".trim($this->session->userdata('gComp'))."',
				'".trim($this->session->userdata('gDept'))."','".trim($this->session->userdata('gCount'))."',
				'".trim($sRefno)."','INS','".trim($this->session->userdata('gSparePart'))."'
		");
		
		// if (trim($sTrx) == 'BL' and trim($sSalesMtdBeli) == '01')
		// {
		// }
		// else
		// {
			// $xSQL = $xSQL.("
				// EXEC stp_jurnalposjual 'POS',' AND fs_refno=''".trim($sRefno)."'''
			   
				// EXEC stp_jurnalposjual 'JURNAL_UNIT_SALES',' AND fs_refno = ''".trim($sRefno)."'''
				// ");
		// }
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function beli_cetak($sKdComp,$sKdDept,$sKdCount,$KdTrans,$sRefno)
	{
		
		$xSQL = ("
			SELECT [Kumpeni]=(SELECT TOP 1 ta.fs_nm_code FROM tm_addr ta (NOLOCK)
					WHERE ta.fs_kd_comp = a.fs_kd_comp AND ta.fs_code = a.fs_kd_comp AND ta.fs_cdtyp='00'),
			[Departement]=a.fs_kd_dept,
			[NamaDepartemen]=(SELECT TOP 1 ta.fs_nm_code FROM tm_addr ta (NOLOCK) 
						WHERE ta.fs_code = a.fs_kd_dept AND ta.fs_count = a.fs_count AND ta.fs_cdtyp='03'),
			[AlamatCabang]=isnull((SELECT TOP 1 x.fs_addr+' '+ISNULL((SELECT TOP 1 xx.fs_nm_vareable FROM tm_vareable xx(NOLOCK) 
											WHERE xx.fs_kd_comp=x.fs_kd_comp 
											AND xx.fs_key='14' 
											AND xx.fs_kd_vareable=x.fs_kd_city),' ')+
							case when len(ltrim(rtrim(isnuLl(x.fs_zip,'')))) > 0 then ' Zip. Code : ' +x.fs_zip else '' end +
							case when len(ltrim(rtrim(isnuLl(x.fs_phone1,'')))) > 0 then ' Phone : ' +x.fs_phone1 else '' end +
							case when len(ltrim(rtrim(isnuLl(x.fs_fax1,'')))) > 0 then ' Fax : ' +x.fs_fax1 else '' end
							
							  FROM tm_addr x(NOLOCK)
							  WHERE x.fs_kd_comp=a.fs_kd_comp
							  AND x.fs_cdtyp='03'
							  AND x.fs_code=a.fs_kd_dept
							  AND x.fs_count=a.fs_count),' '),
				
				[fs_seqno]=a.fs_seqno, [Refno]=a.fs_refno, [DocNo]= b.fs_docno,
				[Tanggal]=convert(varchar(10), convert(datetime,a.fd_refno,103),103),a.fs_kd_product,
				[fs_nm_product]=CASE WHEN left('".trim($sRefno)."',1)='1' THEN 
							(select top 1 x.fs_nm_product from tm_POSproduct  x(NOLOCK) 
								where x.fs_kd_product=a.fs_kd_product AND x.fs_kd_comp=a.fs_kd_comp)
						ELSE
							(select top 1 x.fs_nm_product from tm_product  x(NOLOCK) 
								where x.fs_kd_product=a.fs_kd_product AND x.fs_kd_comp=a.fs_kd_comp)
						END,
				[fn_qty]=a.fn_QtyTR,[fn_unit_price]=a.fn_unitPRC,
				[fn_total]=(a.fn_QtyTR * a.fn_unitPRC),
				[Disc]= a.fn_dscprc,
				[Type]=(select top 1 tm_type.fs_nm_type from tm_type (NOLOCK), tm_product (NOLOCK) 
							where tm_type.fs_kd_type=tm_product.fs_kd_type),
				[fs_nm_unit]=(select top 1 fs_nm_unit from tm_unitCOnvertion (NOLOCK)  
							WHERE  fs_kd_comp=a.fs_kd_comp and fs_kd_unit =a.fs_UnitBill),
				[NMTAX]=isnull((select top 1 tm_tax.fs_nm_tax from tm_tax (NOLOCK) 
							 where tm_tax.fs_kd_tax=b.fS_kd_OTax),''),
				[TAXPERCENT]=isnull((select top 1 tm_tax.fn_percent 	from tm_tax (NOLOCK) 
								 where tm_tax.fs_kd_tax=b.fS_kd_OTax),0),
				[TAXAROUND]=isnull((select top 1 tm_tax.fn_round 	from tm_tax (NOLOCK) 
								where tm_tax.fs_kd_tax=b.fS_kd_OTax),0),			
				[fn_grsamt]=b.fn_grsAmt,
				[fn_netbframt]=b.fn_NetBfrAmt,
				[fn_dscamt]=b.fn_dscAmt,
				[fn_sdscamt]=a.fn_sdscamt,
				[fn_taxamt]=b.fn_TaxAmt,
				[fn_otaxamt]=b.fn_OTaxAmt,
				[fn_addonamt]=b.fn_AddOnAmt,
				[fn_deducamt]=b.fn_DeducAmt,
				[fn_netaftramt]=b.fn_NetAftrAmt,		
				[fn_grdamt]=b.fn_trxamt,					
				[fs_rangka]=ISNULL( z.fs_rangka,''),
				[fs_mesin]=ISNULL( z.fs_machine,''),
				[Customer]=c.fs_nm_code, [CustAddr]=c.fs_addr+'  '+c.fs_phone1
			FROM tx_POSdetail a(NOLOCK), tx_POSheader b(NOLOCK), tm_addr c(NOLOCK),tm_IcRegister z
			WHERE a.fs_kd_comp='". trim($sKdComp)."' 
			AND a.fs_kd_dept='". trim($sKdDept)."'
			AND a.fs_count='". trim($sKdCount)."'
			AND a.fs_kd_trx='". trim($KdTrans)."'
			AND a.fs_refno='". trim($sRefno)."'		
			AND a.fs_kd_comp=b.fs_kd_comp 
			AND a.fs_kd_dept=b.fs_kd_dept 
			AND a.fs_count=b.fs_count 
			AND a.fs_kd_trx=b.fs_kd_trx 
			AND a.fs_refno=b.fs_refno
			AND b.fs_kd_comp=c.fs_kd_comp 		 
			AND b.fs_kd_cussup=c.fs_code
			AND b.fs_countcussup=c.fs_count
			AND c.fs_cdtyp = case a.fs_kd_trx when 'BL' then '01' else '02' end
			AND z.fs_kd_comp=* a.fs_kd_comp 
			AND z.fs_kd_dept=* a.fs_kd_dept
			AND z.fs_count=* a.fs_count	
			AND z.fs_kd_trx=* a.fS_kd_trx
			AND z.fs_refno=* a.fs_refno	
			AND z.fs_seqno=* a.fs_seqno
			ORDER BY a.fs_refno,a.fs_seqno
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>