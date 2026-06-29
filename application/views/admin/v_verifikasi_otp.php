<?php $this->load->view('templates/header'); ?>
<?php $this->load->view('templates/sidebar'); ?>

<div style="display: flex; justify-content: center; align-items: center; min-height: 70vh;">
    
    <div style="background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center; width: 100%; max-width: 450px; border-top: 5px solid #f0ad4e;">
        
        <h2 style="margin-top: 0; color: #333;">Verifikasi Email Petugas</h2>
        
        <p style="color: #666; font-size: 14px; margin-bottom: 25px;">
            <?php if($this->session->flashdata('pesan_sukses_otp')): ?>
                <?php echo $this->session->flashdata('pesan_sukses_otp'); ?><br>
            <?php endif; ?>
            Silakan masukkan 6 digit kode OTP untuk melanjutkan.
        </p>

        <?php if($this->session->flashdata('error_otp')): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; font-weight: bold; font-size: 13px;">
                <?php echo $this->session->flashdata('error_otp'); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo base_url('admin/proses_otp'); ?>" method="post">
            <p>
                <input type="text" name="kode_otp" maxlength="6" placeholder="Masukkan 6 Digit OTP" required autocomplete="off" style="width: 100%; padding: 15px; font-size: 24px; text-align: center; letter-spacing: 5px; border: 2px solid #ccc; border-radius: 6px; box-sizing: border-box; margin-bottom: 20px; font-weight: bold; color: #333;">
            </p>
            
            <p>
                <button type="submit" style="width: 100%; background: #5cb85c; color: white; padding: 12px; border: none; border-radius: 4px; font-size: 16px; font-weight: bold; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    Verifikasi Sekarang
                </button>
            </p>
        </form>

        <p style="margin-top: 20px; font-size: 13px; color: #999;">
            <a href="<?php echo base_url('admin/petugas'); ?>" style="color: #d9534f; text-decoration: none;">&larr; Batal & Kembali Pendaftaran</a>
        </p>
    </div>

</div>

<?php $this->load->view('templates/footer'); ?>