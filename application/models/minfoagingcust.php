<?php

class MInfoAgingCust extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function aging_cust($sTgl,$sTgl2)
	{/*
		$xSQL = ("
			SELECT	a.fs_refno, ISNULL(c.fs_nm_code, '') fs_nm_cust, CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_refno, 105), 105) fd_refno,
					a.fn_trxamt fn_total, a.fn_rmnamt fn_sisa, ISNULL(b.fs_nm_term, '0') fs_nm_term,
					CONVERT(VARCHAR(10), DATEADD(MONTH, CONVERT(INT, ISNULL(b.fs_nm_term, 0)), a.fd_refno), 105) fd_tgl_bayar,
					fd_bulan1 = ISNULL((
						SELECT	TOP 1 CONVERT(VARCHAR(10), CONVERT(DATETIME, x.fd_refno, 105), 105)
						FROM	tx_cbheader x (NOLOCK)
						INNER JOIN tx_cbdetail y (NOLOCK) ON x.fs_kd_comp = y.fs_kd_comp
							AND	x.fs_kd_dept = y.fs_kd_dept
							AND	x.fs_count = y.fs_count
							AND	x.fs_kd_trx = y.fs_kd_trx
							AND	x.fs_kd_strx = y.fs_kd_strx
							AND	x.fs_refno = y.fs_refno
						WHERE	y.fs_kd_refnot = a.fs_refno
							AND	x.fs_kd_trx = '5000'
							AND	x.fs_kd_strx = '0100'
							AND	LEFT(x.fd_refno, 6) =
								'20' + LEFT(CONVERT(VARCHAR(10), CONVERT(DATETIME, DATEADD(MONTH, 1, a.fd_refno), 12), 12), 4)
						ORDER BY x.fs_refno DESC
					), ''),
					fd_bulan2 = ISNULL((
						SELECT	TOP 1 CONVERT(VARCHAR(10), CONVERT(DATETIME, x.fd_refno, 105), 105)
						FROM	tx_cbheader x (NOLOCK)
						INNER JOIN tx_cbdetail y (NOLOCK) ON x.fs_kd_comp = y.fs_kd_comp
							AND	x.fs_kd_dept = y.fs_kd_dept
							AND	x.fs_count = y.fs_count
							AND	x.fs_kd_trx = y.fs_kd_trx
							AND	x.fs_kd_strx = y.fs_kd_strx
							AND	x.fs_refno = y.fs_refno
						WHERE	y.fs_kd_refnot = a.fs_refno
							AND	x.fs_kd_trx = '5000'
							AND	x.fs_kd_strx = '0100'
							AND	LEFT(x.fd_refno, 6) =
								'20' + LEFT(CONVERT(VARCHAR(10), CONVERT(DATETIME, DATEADD(MONTH, 2, a.fd_refno), 12), 12), 4)
						ORDER BY x.fs_refno DESC
					), ''),
					fd_bulan3 = ISNULL((
						SELECT	TOP 1 CONVERT(VARCHAR(10), CONVERT(DATETIME, x.fd_refno, 105), 105)
						FROM	tx_cbheader x (NOLOCK)
						INNER JOIN tx_cbdetail y (NOLOCK) ON x.fs_kd_comp = y.fs_kd_comp
							AND	x.fs_kd_dept = y.fs_kd_dept
							AND	x.fs_count = y.fs_count
							AND	x.fs_kd_trx = y.fs_kd_trx
							AND	x.fs_kd_strx = y.fs_kd_strx
							AND	x.fs_refno = y.fs_refno
						WHERE	y.fs_kd_refnot = a.fs_refno
							AND	x.fs_kd_trx = '5000'
							AND	x.fs_kd_strx = '0100'
							AND	LEFT(x.fd_refno, 6) =
								'20' + LEFT(CONVERT(VARCHAR(10), CONVERT(DATETIME, DATEADD(MONTH, 3, a.fd_refno), 12), 12), 4)
						ORDER BY x.fs_refno DESC
					), ''),
					fd_bulan4 = ISNULL((
						SELECT	TOP 1 CONVERT(VARCHAR(10), CONVERT(DATETIME, x.fd_refno, 105), 105)
						FROM	tx_cbheader x (NOLOCK)
						INNER JOIN tx_cbdetail y (NOLOCK) ON x.fs_kd_comp = y.fs_kd_comp
							AND	x.fs_kd_dept = y.fs_kd_dept
							AND	x.fs_count = y.fs_count
							AND	x.fs_kd_trx = y.fs_kd_trx
							AND	x.fs_kd_strx = y.fs_kd_strx
							AND	x.fs_refno = y.fs_refno
						WHERE	y.fs_kd_refnot = a.fs_refno
							AND	x.fs_kd_trx = '5000'
							AND	x.fs_kd_strx = '0100'
							AND	LEFT(x.fd_refno, 6) =
								'20' + LEFT(CONVERT(VARCHAR(10), CONVERT(DATETIME, DATEADD(MONTH, 4, a.fd_refno), 12), 12), 4)
						ORDER BY x.fs_refno DESC
					), ''),
					fd_bulan5 = ISNULL((
						SELECT	TOP 1 CONVERT(VARCHAR(10), CONVERT(DATETIME, x.fd_refno, 105), 105)
						FROM	tx_cbheader x (NOLOCK)
						INNER JOIN tx_cbdetail y (NOLOCK) ON x.fs_kd_comp = y.fs_kd_comp
							AND	x.fs_kd_dept = y.fs_kd_dept
							AND	x.fs_count = y.fs_count
							AND	x.fs_kd_trx = y.fs_kd_trx
							AND	x.fs_kd_strx = y.fs_kd_strx
							AND	x.fs_refno = y.fs_refno
						WHERE	y.fs_kd_refnot = a.fs_refno
							AND	x.fs_kd_trx = '5000'
							AND	x.fs_kd_strx = '0100'
							AND	LEFT(x.fd_refno, 6) =
								'20' + LEFT(CONVERT(VARCHAR(10), CONVERT(DATETIME, DATEADD(MONTH, 5, a.fd_refno), 12), 12), 4)
						ORDER BY x.fs_refno DESC
					), ''),
					fd_bulan6 = ISNULL((
						SELECT	TOP 1 CONVERT(VARCHAR(10), CONVERT(DATETIME, x.fd_refno, 105), 105)
						FROM	tx_cbheader x (NOLOCK)
						INNER JOIN tx_cbdetail y (NOLOCK) ON x.fs_kd_comp = y.fs_kd_comp
							AND	x.fs_kd_dept = y.fs_kd_dept
							AND	x.fs_count = y.fs_count
							AND	x.fs_kd_trx = y.fs_kd_trx
							AND	x.fs_kd_strx = y.fs_kd_strx
							AND	x.fs_refno = y.fs_refno
						WHERE	y.fs_kd_refnot = a.fs_refno
							AND	x.fs_kd_trx = '5000'
							AND	x.fs_kd_strx = '0100'
							AND	LEFT(x.fd_refno, 6) =
								'20' + LEFT(CONVERT(VARCHAR(10), CONVERT(DATETIME, DATEADD(MONTH, 6, a.fd_refno), 12), 12), 4)
						ORDER BY x.fs_refno DESC
					), ''),
					fd_bulan7 = ISNULL((
						SELECT	TOP 1 CONVERT(VARCHAR(10), CONVERT(DATETIME, x.fd_refno, 105), 105)
						FROM	tx_cbheader x (NOLOCK)
						INNER JOIN tx_cbdetail y (NOLOCK) ON x.fs_kd_comp = y.fs_kd_comp
							AND	x.fs_kd_dept = y.fs_kd_dept
							AND	x.fs_count = y.fs_count
							AND	x.fs_kd_trx = y.fs_kd_trx
							AND	x.fs_kd_strx = y.fs_kd_strx
							AND	x.fs_refno = y.fs_refno
						WHERE	y.fs_kd_refnot = a.fs_refno
							AND	x.fs_kd_trx = '5000'
							AND	x.fs_kd_strx = '0100'
							AND	LEFT(x.fd_refno, 6) =
								'20' + LEFT(CONVERT(VARCHAR(10), CONVERT(DATETIME, DATEADD(MONTH, 7, a.fd_refno), 12), 12), 4)
						ORDER BY x.fs_refno DESC
					), ''),
					fd_bulan8 = ISNULL((
						SELECT	TOP 1 CONVERT(VARCHAR(10), CONVERT(DATETIME, x.fd_refno, 105), 105)
						FROM	tx_cbheader x (NOLOCK)
						INNER JOIN tx_cbdetail y (NOLOCK) ON x.fs_kd_comp = y.fs_kd_comp
							AND	x.fs_kd_dept = y.fs_kd_dept
							AND	x.fs_count = y.fs_count
							AND	x.fs_kd_trx = y.fs_kd_trx
							AND	x.fs_kd_strx = y.fs_kd_strx
							AND	x.fs_refno = y.fs_refno
						WHERE	y.fs_kd_refnot = a.fs_refno
							AND	x.fs_kd_trx = '5000'
							AND	x.fs_kd_strx = '0100'
							AND	LEFT(x.fd_refno, 6) =
								'20' + LEFT(CONVERT(VARCHAR(10), CONVERT(DATETIME, DATEADD(MONTH, 8, a.fd_refno), 12), 12), 4)
						ORDER BY x.fs_refno DESC
					), ''),
					fd_bulan9 = ISNULL((
						SELECT	TOP 1 CONVERT(VARCHAR(10), CONVERT(DATETIME, x.fd_refno, 105), 105)
						FROM	tx_cbheader x (NOLOCK)
						INNER JOIN tx_cbdetail y (NOLOCK) ON x.fs_kd_comp = y.fs_kd_comp
							AND	x.fs_kd_dept = y.fs_kd_dept
							AND	x.fs_count = y.fs_count
							AND	x.fs_kd_trx = y.fs_kd_trx
							AND	x.fs_kd_strx = y.fs_kd_strx
							AND	x.fs_refno = y.fs_refno
						WHERE	y.fs_kd_refnot = a.fs_refno
							AND	x.fs_kd_trx = '5000'
							AND	x.fs_kd_strx = '0100'
							AND	LEFT(x.fd_refno, 6) =
								'20' + LEFT(CONVERT(VARCHAR(10), CONVERT(DATETIME, DATEADD(MONTH, 9, a.fd_refno), 12), 12), 4)
						ORDER BY x.fs_refno DESC
					), ''),
					fd_bulan10 = ISNULL((
						SELECT	TOP 1 CONVERT(VARCHAR(10), CONVERT(DATETIME, x.fd_refno, 105), 105)
						FROM	tx_cbheader x (NOLOCK)
						INNER JOIN tx_cbdetail y (NOLOCK) ON x.fs_kd_comp = y.fs_kd_comp
							AND	x.fs_kd_dept = y.fs_kd_dept
							AND	x.fs_count = y.fs_count
							AND	x.fs_kd_trx = y.fs_kd_trx
							AND	x.fs_kd_strx = y.fs_kd_strx
							AND	x.fs_refno = y.fs_refno
						WHERE	y.fs_kd_refnot = a.fs_refno
							AND	x.fs_kd_trx = '5000'
							AND	x.fs_kd_strx = '0100'
							AND	LEFT(x.fd_refno, 6) =
								'20' + LEFT(CONVERT(VARCHAR(10), CONVERT(DATETIME, DATEADD(MONTH, 10, a.fd_refno), 12), 12), 4)
						ORDER BY x.fs_refno DESC
					), ''),
					fd_bulan11 = ISNULL((
						SELECT	TOP 1 CONVERT(VARCHAR(10), CONVERT(DATETIME, x.fd_refno, 105), 105)
						FROM	tx_cbheader x (NOLOCK)
						INNER JOIN tx_cbdetail y (NOLOCK) ON x.fs_kd_comp = y.fs_kd_comp
							AND	x.fs_kd_dept = y.fs_kd_dept
							AND	x.fs_count = y.fs_count
							AND	x.fs_kd_trx = y.fs_kd_trx
							AND	x.fs_kd_strx = y.fs_kd_strx
							AND	x.fs_refno = y.fs_refno
						WHERE	y.fs_kd_refnot = a.fs_refno
							AND	x.fs_kd_trx = '5000'
							AND	x.fs_kd_strx = '0100'
							AND	LEFT(x.fd_refno, 6) =
								'20' + LEFT(CONVERT(VARCHAR(10), CONVERT(DATETIME, DATEADD(MONTH, 11, a.fd_refno), 12), 12), 4)
						ORDER BY x.fs_refno DESC
					), ''),
					fd_bulan12 = ISNULL((
						SELECT	TOP 1 CONVERT(VARCHAR(10), CONVERT(DATETIME, x.fd_refno, 105), 105)
						FROM	tx_cbheader x (NOLOCK)
						INNER JOIN tx_cbdetail y (NOLOCK) ON x.fs_kd_comp = y.fs_kd_comp
							AND	x.fs_kd_dept = y.fs_kd_dept
							AND	x.fs_count = y.fs_count
							AND	x.fs_kd_trx = y.fs_kd_trx
							AND	x.fs_kd_strx = y.fs_kd_strx
							AND	x.fs_refno = y.fs_refno
						WHERE	y.fs_kd_refnot = a.fs_refno
							AND	x.fs_kd_trx = '5000'
							AND	x.fs_kd_strx = '0100'
							AND	LEFT(x.fd_refno, 6) =
								'20' + LEFT(CONVERT(VARCHAR(10), CONVERT(DATETIME, DATEADD(MONTH, 12, a.fd_refno), 12), 12), 4)
						ORDER BY x.fs_refno DESC
					), '')
			FROM	tx_posheader a (NOLOCK)
			LEFT JOIN tm_term b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_term = b.fs_kd_term
			LEFT JOIN tm_addr c (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_cussup = c.fs_code
				AND	a.fs_countcussup = c.fs_count
				AND	c.fs_cdtyp = '02'
			WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	a.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND	a.fs_count = '".trim($this->session->userdata('gCount'))."'
				AND	a.fs_kd_trx = 'JL'
				AND	a.fs_kd_payment IN ('0','1')
			ORDER BY a.fs_refno
		");*/
		$xSQL = ("
			SELECT	a.fs_refno, ISNULL(c.fs_nm_code, '') fs_nm_cust, CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_refno, 105), 105) fd_refno,
					a.fn_trxamt fn_total, a.fn_rmnamt fn_sisa, ISNULL(b.fs_nm_term, '0') fs_nm_term,
					CONVERT(VARCHAR(10), DATEADD(MONTH, CONVERT(INT, ISNULL(b.fs_nm_term, 0)), a.fd_refno), 105) fd_tgl_bayar,
					ISNULL(d.fd_bulan1, '') fd_bulan1, ISNULL(d.fn_bayar1, 0) fn_bayar1,
					ISNULL(e.fd_bulan2, '') fd_bulan2, ISNULL(e.fn_bayar2, 0) fn_bayar2,
					ISNULL(f.fd_bulan3, '') fd_bulan3, ISNULL(f.fn_bayar3, 0) fn_bayar3,
					ISNULL(g.fd_bulan4, '') fd_bulan4, ISNULL(g.fn_bayar4, 0) fn_bayar4,
					ISNULL(h.fd_bulan5, '') fd_bulan5, ISNULL(h.fn_bayar5, 0) fn_bayar5,
					ISNULL(i.fd_bulan6, '') fd_bulan6, ISNULL(i.fn_bayar6, 0) fn_bayar6,
					ISNULL(j.fd_bulan7, '') fd_bulan7, ISNULL(j.fn_bayar7, 0) fn_bayar7,
					ISNULL(k.fd_bulan8, '') fd_bulan8, ISNULL(k.fn_bayar8, 0) fn_bayar8,
					ISNULL(l.fd_bulan9, '') fd_bulan9, ISNULL(l.fn_bayar9, 0) fn_bayar9,
					ISNULL(m.fd_bulan10, '') fd_bulan10, ISNULL(m.fn_bayar10, 0) fn_bayar10,
					ISNULL(n.fd_bulan11, '') fd_bulan11, ISNULL(n.fn_bayar11, 0) fn_bayar11,
					ISNULL(o.fd_bulan12, '') fd_bulan12, ISNULL(o.fn_bayar12, 0) fn_bayar12
			FROM	tx_posheader a (NOLOCK)
			LEFT JOIN tm_term b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_term = b.fs_kd_term
			LEFT JOIN tm_addr c (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_cussup = c.fs_code
				AND	a.fs_countcussup = c.fs_count
				AND	c.fs_cdtyp = '02'
			LEFT JOIN (
				SELECT	x.fs_kd_comp, y.fs_kd_refnot fs_refnot, x.fd_refno,
						CONVERT(VARCHAR(10), CONVERT(DATETIME, x.fd_refno, 105), 105) fd_bulan1,
						CONVERT(NUMERIC(35,0), y.fn_trxamtt) fn_bayar1
				FROM	tx_cbheader x (NOLOCK)
				INNER JOIN tx_cbdetail y (NOLOCK) ON x.fs_kd_comp = y.fs_kd_comp
					AND	x.fs_kd_dept = y.fs_kd_dept
					AND	x.fs_count = y.fs_count
					AND	x.fs_kd_trx = y.fs_kd_trx
					AND	x.fs_kd_strx = y.fs_kd_strx
					AND	x.fs_refno = y.fs_refno
				WHERE	x.fs_kd_trx = '5000'
					AND	x.fs_kd_strx = '0100'
			) d ON a.fs_kd_comp = d.fs_kd_comp
				AND	a.fs_refno = d.fs_refnot
				AND	LEFT(d.fd_refno, 6) =
						'20' + LEFT(CONVERT(VARCHAR(10), CONVERT(DATETIME, DATEADD(MONTH, 1, a.fd_refno), 12), 12), 4)
			LEFT JOIN (
				SELECT	x.fs_kd_comp, y.fs_kd_refnot fs_refnot, x.fd_refno,
						CONVERT(VARCHAR(10), CONVERT(DATETIME, x.fd_refno, 105), 105) fd_bulan2,
						CONVERT(NUMERIC(35,0), y.fn_trxamtt) fn_bayar2
				FROM	tx_cbheader x (NOLOCK)
				INNER JOIN tx_cbdetail y (NOLOCK) ON x.fs_kd_comp = y.fs_kd_comp
					AND	x.fs_kd_dept = y.fs_kd_dept
					AND	x.fs_count = y.fs_count
					AND	x.fs_kd_trx = y.fs_kd_trx
					AND	x.fs_kd_strx = y.fs_kd_strx
					AND	x.fs_refno = y.fs_refno
				WHERE	x.fs_kd_trx = '5000'
					AND	x.fs_kd_strx = '0100'
			) e ON a.fs_kd_comp = e.fs_kd_comp
				AND	a.fs_refno = e.fs_refnot
				AND	LEFT(e.fd_refno, 6) =
						'20' + LEFT(CONVERT(VARCHAR(10), CONVERT(DATETIME, DATEADD(MONTH, 2, a.fd_refno), 12), 12), 4)
			LEFT JOIN (
				SELECT	x.fs_kd_comp, y.fs_kd_refnot fs_refnot, x.fd_refno,
						CONVERT(VARCHAR(10), CONVERT(DATETIME, x.fd_refno, 105), 105) fd_bulan3,
						CONVERT(NUMERIC(35,0), y.fn_trxamtt) fn_bayar3
				FROM	tx_cbheader x (NOLOCK)
				INNER JOIN tx_cbdetail y (NOLOCK) ON x.fs_kd_comp = y.fs_kd_comp
					AND	x.fs_kd_dept = y.fs_kd_dept
					AND	x.fs_count = y.fs_count
					AND	x.fs_kd_trx = y.fs_kd_trx
					AND	x.fs_kd_strx = y.fs_kd_strx
					AND	x.fs_refno = y.fs_refno
				WHERE	x.fs_kd_trx = '5000'
					AND	x.fs_kd_strx = '0100'
			) f ON a.fs_kd_comp = f.fs_kd_comp
				AND	a.fs_refno = f.fs_refnot
				AND	LEFT(f.fd_refno, 6) =
						'20' + LEFT(CONVERT(VARCHAR(10), CONVERT(DATETIME, DATEADD(MONTH, 3, a.fd_refno), 12), 12), 4)
			LEFT JOIN (
				SELECT	x.fs_kd_comp, y.fs_kd_refnot fs_refnot, x.fd_refno,
						CONVERT(VARCHAR(10), CONVERT(DATETIME, x.fd_refno, 105), 105) fd_bulan4,
						CONVERT(NUMERIC(35,0), y.fn_trxamtt) fn_bayar4
				FROM	tx_cbheader x (NOLOCK)
				INNER JOIN tx_cbdetail y (NOLOCK) ON x.fs_kd_comp = y.fs_kd_comp
					AND	x.fs_kd_dept = y.fs_kd_dept
					AND	x.fs_count = y.fs_count
					AND	x.fs_kd_trx = y.fs_kd_trx
					AND	x.fs_kd_strx = y.fs_kd_strx
					AND	x.fs_refno = y.fs_refno
				WHERE	x.fs_kd_trx = '5000'
					AND	x.fs_kd_strx = '0100'
			) g ON a.fs_kd_comp = g.fs_kd_comp
				AND	a.fs_refno = g.fs_refnot
				AND	LEFT(g.fd_refno, 6) =
						'20' + LEFT(CONVERT(VARCHAR(10), CONVERT(DATETIME, DATEADD(MONTH, 4, a.fd_refno), 12), 12), 4)
			LEFT JOIN (
				SELECT	x.fs_kd_comp, y.fs_kd_refnot fs_refnot, x.fd_refno,
						CONVERT(VARCHAR(10), CONVERT(DATETIME, x.fd_refno, 105), 105) fd_bulan5,
						CONVERT(NUMERIC(35,0), y.fn_trxamtt) fn_bayar5
				FROM	tx_cbheader x (NOLOCK)
				INNER JOIN tx_cbdetail y (NOLOCK) ON x.fs_kd_comp = y.fs_kd_comp
					AND	x.fs_kd_dept = y.fs_kd_dept
					AND	x.fs_count = y.fs_count
					AND	x.fs_kd_trx = y.fs_kd_trx
					AND	x.fs_kd_strx = y.fs_kd_strx
					AND	x.fs_refno = y.fs_refno
				WHERE	x.fs_kd_trx = '5000'
					AND	x.fs_kd_strx = '0100'
			) h ON a.fs_kd_comp = h.fs_kd_comp
				AND	a.fs_refno = h.fs_refnot
				AND	LEFT(h.fd_refno, 6) =
						'20' + LEFT(CONVERT(VARCHAR(10), CONVERT(DATETIME, DATEADD(MONTH, 5, a.fd_refno), 12), 12), 4)
			LEFT JOIN (
				SELECT	x.fs_kd_comp, y.fs_kd_refnot fs_refnot, x.fd_refno,
						CONVERT(VARCHAR(10), CONVERT(DATETIME, x.fd_refno, 105), 105) fd_bulan6,
						CONVERT(NUMERIC(35,0), y.fn_trxamtt) fn_bayar6
				FROM	tx_cbheader x (NOLOCK)
				INNER JOIN tx_cbdetail y (NOLOCK) ON x.fs_kd_comp = y.fs_kd_comp
					AND	x.fs_kd_dept = y.fs_kd_dept
					AND	x.fs_count = y.fs_count
					AND	x.fs_kd_trx = y.fs_kd_trx
					AND	x.fs_kd_strx = y.fs_kd_strx
					AND	x.fs_refno = y.fs_refno
				WHERE	x.fs_kd_trx = '5000'
					AND	x.fs_kd_strx = '0100'
			) i ON a.fs_kd_comp = i.fs_kd_comp
				AND	a.fs_refno = i.fs_refnot
				AND	LEFT(i.fd_refno, 6) =
						'20' + LEFT(CONVERT(VARCHAR(10), CONVERT(DATETIME, DATEADD(MONTH, 6, a.fd_refno), 12), 12), 4)
			LEFT JOIN (
				SELECT	x.fs_kd_comp, y.fs_kd_refnot fs_refnot, x.fd_refno,
						CONVERT(VARCHAR(10), CONVERT(DATETIME, x.fd_refno, 105), 105) fd_bulan7,
						CONVERT(NUMERIC(35,0), y.fn_trxamtt) fn_bayar7
				FROM	tx_cbheader x (NOLOCK)
				INNER JOIN tx_cbdetail y (NOLOCK) ON x.fs_kd_comp = y.fs_kd_comp
					AND	x.fs_kd_dept = y.fs_kd_dept
					AND	x.fs_count = y.fs_count
					AND	x.fs_kd_trx = y.fs_kd_trx
					AND	x.fs_kd_strx = y.fs_kd_strx
					AND	x.fs_refno = y.fs_refno
				WHERE	x.fs_kd_trx = '5000'
					AND	x.fs_kd_strx = '0100'
			) j ON a.fs_kd_comp = j.fs_kd_comp
				AND	a.fs_refno = j.fs_refnot
				AND	LEFT(j.fd_refno, 6) =
						'20' + LEFT(CONVERT(VARCHAR(10), CONVERT(DATETIME, DATEADD(MONTH, 7, a.fd_refno), 12), 12), 4)
			LEFT JOIN (
				SELECT	x.fs_kd_comp, y.fs_kd_refnot fs_refnot, x.fd_refno,
						CONVERT(VARCHAR(10), CONVERT(DATETIME, x.fd_refno, 105), 105) fd_bulan8,
						CONVERT(NUMERIC(35,0), y.fn_trxamtt) fn_bayar8
				FROM	tx_cbheader x (NOLOCK)
				INNER JOIN tx_cbdetail y (NOLOCK) ON x.fs_kd_comp = y.fs_kd_comp
					AND	x.fs_kd_dept = y.fs_kd_dept
					AND	x.fs_count = y.fs_count
					AND	x.fs_kd_trx = y.fs_kd_trx
					AND	x.fs_kd_strx = y.fs_kd_strx
					AND	x.fs_refno = y.fs_refno
				WHERE	x.fs_kd_trx = '5000'
					AND	x.fs_kd_strx = '0100'
			) k ON a.fs_kd_comp = k.fs_kd_comp
				AND	a.fs_refno = k.fs_refnot
				AND	LEFT(k.fd_refno, 6) =
						'20' + LEFT(CONVERT(VARCHAR(10), CONVERT(DATETIME, DATEADD(MONTH, 8, a.fd_refno), 12), 12), 4)
			LEFT JOIN (
				SELECT	x.fs_kd_comp, y.fs_kd_refnot fs_refnot, x.fd_refno,
						CONVERT(VARCHAR(10), CONVERT(DATETIME, x.fd_refno, 105), 105) fd_bulan9,
						CONVERT(NUMERIC(35,0), y.fn_trxamtt) fn_bayar9
				FROM	tx_cbheader x (NOLOCK)
				INNER JOIN tx_cbdetail y (NOLOCK) ON x.fs_kd_comp = y.fs_kd_comp
					AND	x.fs_kd_dept = y.fs_kd_dept
					AND	x.fs_count = y.fs_count
					AND	x.fs_kd_trx = y.fs_kd_trx
					AND	x.fs_kd_strx = y.fs_kd_strx
					AND	x.fs_refno = y.fs_refno
				WHERE	x.fs_kd_trx = '5000'
					AND	x.fs_kd_strx = '0100'
			) l ON a.fs_kd_comp = l.fs_kd_comp
				AND	a.fs_refno = l.fs_refnot
				AND	LEFT(l.fd_refno, 6) =
						'20' + LEFT(CONVERT(VARCHAR(10), CONVERT(DATETIME, DATEADD(MONTH, 9, a.fd_refno), 12), 12), 4)
			LEFT JOIN (
				SELECT	x.fs_kd_comp, y.fs_kd_refnot fs_refnot, x.fd_refno,
						CONVERT(VARCHAR(10), CONVERT(DATETIME, x.fd_refno, 105), 105) fd_bulan10,
						CONVERT(NUMERIC(35,0), y.fn_trxamtt) fn_bayar10
				FROM	tx_cbheader x (NOLOCK)
				INNER JOIN tx_cbdetail y (NOLOCK) ON x.fs_kd_comp = y.fs_kd_comp
					AND	x.fs_kd_dept = y.fs_kd_dept
					AND	x.fs_count = y.fs_count
					AND	x.fs_kd_trx = y.fs_kd_trx
					AND	x.fs_kd_strx = y.fs_kd_strx
					AND	x.fs_refno = y.fs_refno
				WHERE	x.fs_kd_trx = '5000'
					AND	x.fs_kd_strx = '0100'
			) m ON a.fs_kd_comp = m.fs_kd_comp
				AND	a.fs_refno = m.fs_refnot
				AND	LEFT(m.fd_refno, 6) =
						'20' + LEFT(CONVERT(VARCHAR(10), CONVERT(DATETIME, DATEADD(MONTH, 10, a.fd_refno), 12), 12), 4)
			LEFT JOIN (
				SELECT	x.fs_kd_comp, y.fs_kd_refnot fs_refnot, x.fd_refno,
						CONVERT(VARCHAR(10), CONVERT(DATETIME, x.fd_refno, 105), 105) fd_bulan11,
						CONVERT(NUMERIC(35,0), y.fn_trxamtt) fn_bayar11
				FROM	tx_cbheader x (NOLOCK)
				INNER JOIN tx_cbdetail y (NOLOCK) ON x.fs_kd_comp = y.fs_kd_comp
					AND	x.fs_kd_dept = y.fs_kd_dept
					AND	x.fs_count = y.fs_count
					AND	x.fs_kd_trx = y.fs_kd_trx
					AND	x.fs_kd_strx = y.fs_kd_strx
					AND	x.fs_refno = y.fs_refno
				WHERE	x.fs_kd_trx = '5000'
					AND	x.fs_kd_strx = '0100'
			) n ON a.fs_kd_comp = n.fs_kd_comp
				AND	a.fs_refno = n.fs_refnot
				AND	LEFT(n.fd_refno, 6) =
						'20' + LEFT(CONVERT(VARCHAR(10), CONVERT(DATETIME, DATEADD(MONTH, 11, a.fd_refno), 12), 12), 4)
			LEFT JOIN (
				SELECT	x.fs_kd_comp, y.fs_kd_refnot fs_refnot, x.fd_refno,
						CONVERT(VARCHAR(10), CONVERT(DATETIME, x.fd_refno, 105), 105) fd_bulan12,
						CONVERT(NUMERIC(35,0), y.fn_trxamtt) fn_bayar12
				FROM	tx_cbheader x (NOLOCK)
				INNER JOIN tx_cbdetail y (NOLOCK) ON x.fs_kd_comp = y.fs_kd_comp
					AND	x.fs_kd_dept = y.fs_kd_dept
					AND	x.fs_count = y.fs_count
					AND	x.fs_kd_trx = y.fs_kd_trx
					AND	x.fs_kd_strx = y.fs_kd_strx
					AND	x.fs_refno = y.fs_refno
				WHERE	x.fs_kd_trx = '5000'
					AND	x.fs_kd_strx = '0100'
			) o ON a.fs_kd_comp = o.fs_kd_comp
				AND	a.fs_refno = o.fs_refnot
				AND	LEFT(o.fd_refno, 6) =
						'20' + LEFT(CONVERT(VARCHAR(10), CONVERT(DATETIME, DATEADD(MONTH, 12, a.fd_refno), 12), 12), 4)
			WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	a.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND	a.fs_count = '".trim($this->session->userdata('gCount'))."'
				AND	a.fs_kd_trx = 'JL'
				AND	a.fs_kd_payment IN ('0','1')
			ORDER BY a.fs_refno
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>