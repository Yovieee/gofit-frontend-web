<?php

namespace App\Controllers;

class Depositkelas extends BaseController
{
    public function getIndex()
    {
        return $this->respond($this->db->query(
            'SELECT * FROM deposit_kelass'
            )->getResultArray(), 200);
    }
    public function postCreate()
    {
        $data = $this->request->getPost();
        $this->db->query(
            'INSERT INTO deposit_kelass (
                NO_STRUK_DEPOSIT_KELAS,
                MEM_ID_USER,
                ID_MEMBER,
                ID_KELAS,
                ID_PROMO,
                PEG_ID_USER,
                ID_PEGAWAI,
                TANGGAL_DEPOSIT_KELAS,
                TANGGAL_KADALUARSA_DEPOSIT_KELAS,
                JUMLAH_DEPOSIT_KELAS,
                BONUS_DEPOSIT_KELAS)
            VALUES ("'
                . base64_encode(random_bytes(24)) . '", "'
                . $data['MEM_ID_USER'] . '", "'
                . $data['ID_MEMBER'] . '", "'
                . $data['ID_KELAS'] . '", "'
                . $data['ID_PROMO'] . '", "'
                . $data['PEG_ID_USER'] . '", "'
                . $data['ID_PEGAWAI'] . '", "'
                . date("Y-m-d") . '", "'
                . date('Y-m-d',strtotime("+1 year")) . '", "'
                . $data['JUMLAH_DEPOSIT_KELAS'] . '", "'
                . $data['BONUS_DEPOSIT_KELAS']
                . '");'
            );
        return $this->respondCreated($data);
    }
    public function postDelete()
    {
        $data = $this->request->getPost();
        $this->db->query(
            'UPDATE deposit_kelass
            SET IS_DELETED_DEPOSIT_KELAS = 1
            WHERE ID_DEPOSIT_KELAS = "' . $data['ID_DEPOSIT_KELAS'] . '";'
        );
        return $this->respondDeleted($data);
    }
}