<!-- Modal Detail Record -->
<div class="modal fade" id="detailModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header" style="background: var(--primary-gradient); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-info-circle me-2"></i>Detail Catatan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="text-muted small">Tanggal</label>
                        <p class="fw-semibold mb-0">{{ $detailRecordDate }}</p>
                    </div>
                    <div class="col-6">
                        <label class="text-muted small">Tipe</label>
                        <p class="mb-0">
                            @if ($detailType == 'income')
                                <span class="badge rounded-pill text-bg-success">Pemasukan</span>
                            @else
                                <span class="badge rounded-pill text-bg-danger">Pengeluaran</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-6">
                        <label class="text-muted small">Jumlah</label>
                        <p class="fw-bold mb-0">Rp {{ number_format($detailAmount, 0, ',', '.') }}</p>
                    </div>
                    <div class="col-12">
                        <label class="text-muted small">Deskripsi</label>
                        <div class="p-3 bg-light rounded">{!! $detailDescription !!}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
