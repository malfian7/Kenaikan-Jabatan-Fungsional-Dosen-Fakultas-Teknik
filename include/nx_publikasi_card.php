<?php
/* Kartu "Publikasi syarat khusus": daftar + formulir tambah. Hanya untuk kenaikan jenjang. */
include_once __DIR__ . '/nx_aturan.php';

function nx_publikasi_card($koneksi, $nip, $id_pengajuan, $jenjang, $boleh_ubah)
{
	$jenis = nx_jenis_publikasi(); $per = nx_peringkat_per_jenis(); $peran = nx_peran_opsi();
	$rows = (array) $koneksi->GetAll('SELECT id_publikasi, judul, jenis, peringkat, peran, tautan FROM publikasi WHERE id_pengajuan = ? ORDER BY id_publikasi', array($id_pengajuan));
	$q = 'nip_dosen=' . rawurlencode($nip) . '&id_pengajuan=' . rawurlencode($id_pengajuan);
	echo '<h1></h1><div class="card" id="publikasi"><div class="card-header bg-primary text-light"><b>PUBLIKASI SYARAT KHUSUS</b></div><div class="card-body">';
	echo '<p class="text-muted" style="font-size:13px">' . nx_e(nx_syarat_publikasi_teks($jenjang)) . '</p>';
	if ($rows) {
		echo '<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>Judul</th><th>Jenis</th><th>Peringkat</th><th>Peran</th><th width="70"><center>AK acuan</center></th><th width="110"><center>Action</center></th></tr></thead><tbody>';
		foreach ($rows as $r) {
			$ak = nx_ak_acuan_publikasi($r[2], $r[3]);
			echo '<tr><td><a target="_blank" rel="noopener" href="' . nx_e($r[5]) . '">' . nx_e($r[1]) . '</a></td>';
			echo '<td>' . nx_e(isset($jenis[$r[2]]) ? $jenis[$r[2]] : $r[2]) . '</td>';
			echo '<td>' . nx_e(isset($per[$r[2]][$r[3]]) ? $per[$r[2]][$r[3]] : $r[3]) . '</td>';
			echo '<td>' . nx_e(isset($peran[$r[4]]) ? $peran[$r[4]] : $r[4]) . '</td>';
			echo '<td><center>' . ($ak === null ? '-' : nx_e($ak)) . '</center></td><td><center>';
			if ($boleh_ubah) {
				echo '<form action="dosen_publikasi_hapus_code.php?' . $q . '&id_publikasi=' . (int) $r[0] . '" method="post" class="nx-inline-form"><button type="submit" class="btn btn-sm btn-danger" data-confirm-title="Hapus publikasi ini?" data-confirm-text="Publikasi dihapus dari pengajuan dan tidak dihitung sebagai syarat khusus." data-confirm-label="Hapus" data-confirm-tone="danger">Hapus</button></form>';
			}
			echo '</center></td></tr>';
		}
		echo '</tbody></table></div><p class="text-muted" style="font-size:12px">AK acuan mengikuti Tabel 2 juknis (hal. 14-19) dan tidak dijumlahkan ke total; angka kredit resmi ditetapkan penilai.</p>';
	} else {
		echo '<p class="text-muted">Belum ada publikasi.</p>';
	}
	if ($boleh_ubah) {
		echo '<form action="dosen_publikasi_input_code.php?' . $q . '" method="post" class="nx-pub-form" id="nx-pub-form">';
		echo '<div class="form-row">';
		echo '<div class="form-group col-md-12"><label for="pub_judul">Judul publikasi</label><input type="text" class="form-control" id="pub_judul" name="judul" required minlength="5" maxlength="255"></div>';
		echo '<div class="form-group col-md-4"><label for="pub_jenis">Jenis</label><select class="form-control" id="pub_jenis" name="jenis" required><option value="">Pilih jenis</option>';
		foreach ($jenis as $k => $v) echo '<option value="' . nx_e($k) . '">' . nx_e($v) . '</option>';
		echo '</select></div>';
		echo '<div class="form-group col-md-4"><label for="pub_peringkat">Peringkat</label><select class="form-control" id="pub_peringkat" name="peringkat" required><option value="">Pilih jenis dulu</option></select></div>';
		echo '<div class="form-group col-md-4"><label for="pub_peran">Peran Anda</label><select class="form-control" id="pub_peran" name="peran" required><option value="">Pilih peran</option>';
		foreach ($peran as $k => $v) echo '<option value="' . nx_e($k) . '">' . nx_e($v) . '</option>';
		echo '</select></div>';
		echo '<div class="form-group col-md-12"><label for="pub_tautan">Tautan bukti</label><input type="text" class="form-control" id="pub_tautan" name="tautan" required placeholder="https://..."></div>';
		echo '</div><button type="submit" class="btn btn-success">TAMBAH PUBLIKASI</button></form>';
		echo '<script nonce="' . nx_nonce() . '">(function(){var opt=' . json_encode($per) . ',j=document.getElementById("pub_jenis"),p=document.getElementById("pub_peringkat"),r=document.getElementById("pub_peran");';
		echo 'j.addEventListener("change",function(){p.innerHTML="";var o=document.createElement("option");o.value="";o.textContent=j.value?"Pilih peringkat":"Pilih jenis dulu";p.appendChild(o);var m=opt[j.value]||{};Object.keys(m).forEach(function(k){var e=document.createElement("option");e.value=k;e.textContent=m[k];p.appendChild(e);});r.disabled=(j.value==="karya_seni");if(r.disabled){r.removeAttribute("required");r.value="";}else{r.setAttribute("required","");}});})();</script>';
	}
	echo '</div></div>';
}
