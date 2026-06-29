<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!-- Flash Messages -->
<?php if($this->session->flashdata('sukses_profil')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> <?= $this->session->flashdata('sukses_profil') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if($this->session->flashdata('error_profil')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> <?= $this->session->flashdata('error_profil') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="fas fa-user-circle"></i> Profil Saya</h2>
        <p class="text-muted">Informasi akun Kepala Laboratorium</p>
    </div>
    <span class="badge bg-danger" style="padding: 8px 20px; font-size: 14px;">KEPALA LAB</span>
</div>

<?php if(isset($user) && !empty($user)): ?>
<div class="row">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <img src="<?= base_url('assets/uploads/profil/' . ($user['foto_profil'] ?? 'default.jpg')) ?>" 
                     class="rounded-circle img-thumbnail" width="150" height="150" style="object-fit:cover;">
                <h5 class="mt-3"><?= $user['nama_lengkap'] ?></h5>
                <span class="badge bg-danger"><?= strtoupper(str_replace('_', ' ', $user['role'])) ?></span>
                
                <hr>
                <form action="<?= base_url('kalab/update_profil') ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Ganti Foto Profil</label>
                        <input type="file" name="foto_profil" class="form-control" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG. Max: 2MB</small>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-upload"></i> Upload Foto
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="<?= base_url('kalab/update_profil') ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" value="<?= $user['nama_lengkap'] ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nomor Induk</label>
                        <input type="text" class="form-control" value="<?= $user['nomor_induk'] ?>" disabled>
                        <small class="text-muted">Nomor induk tidak dapat diubah</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" class="form-control" value="<?= $user['email'] ?>" disabled>
                        <small class="text-muted">Email tidak dapat diubah</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Program Studi</label>
                        <input type="text" class="form-control" value="<?= $user['program_studi'] ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Password Baru</label>
                        <input type="password" name="password_baru" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password">
                        <small class="text-muted">Minimal 6 karakter</small>
                    </div>

                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Update Profil
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="alert alert-danger">Data user tidak ditemukan.</div>
<?php endif; ?>