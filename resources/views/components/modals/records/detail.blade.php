<!-- Modal Detail Record -->
<div class="modal fade" id="detailRecordModal" tabindex="-1" aria-labelledby="detailRecordModalLabel" aria-hidden="true" wire:ignore.self>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailRecordModalLabel">Detail Catatan Keuangan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <dl class="row">
          <dt class="col-sm-4">Tanggal</dt>
          <dd class="col-sm-8">{{ $detailRecordDate }}</dd>

          <dt class="col-sm-4">Deskripsi</dt>
          <dd class="col-sm-8">{!! $detailDescription !!}</dd>

          <dt class="col-sm-4">Tipe</dt>
          <dd class="col-sm-8">
            @if ($detailType == 'income')
              <span class="badge bg-success">Pemasukan</span>
            @else
              <span class="badge bg-danger">Pengeluaran</span>
            @endif
          </dd>

          <dt class="col-sm-4">Jumlah</dt>
          <dd class="col-sm-8">Rp {{ number_format($detailAmount, 2, ',', '.') }}</dd>

          <dt class="col-sm-4">Lampiran</dt>
          <dd class="col-sm-8">
            @if ($detailAttachment)
              <a href="{{ asset('storage/' . $detailAttachment) }}" target="_blank">Lihat Lampiran</a>
            @else
              <span class="text-muted">Tidak ada lampiran</span>
            @endif
          </dd>
        </dl>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
