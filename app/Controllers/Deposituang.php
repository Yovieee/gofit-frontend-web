<?php

namespace App\Controllers;

class Deposituang extends BaseController
{
    public function getIndex()
    {
        return $this->respond($this->db->query(
            'SELECT * FROM deposit_uangs'
            )->getResultArray(), 200);
    }
    public function postCreate()
    {
        $data = $this->request->getPost();
        $this->db->query(
            'INSERT INTO deposit_uangs (
                NO_STRUK_DEPOSIT_UANG,
                MEM_ID_USER,
                ID_MEMBER,
                ID_PROMO,
                PEG_ID_USER,
                ID_PEGAWAI,
                JUMLAH_DEPOSIT_UANG,
                BONUS_DEPOSIT_UANG,
                TANGGAL_DEPOSIT_UANG)
            VALUES ("'
                . base64_encode(random_bytes(24)) . '", "'
                . $data['MEM_ID_USER'] . '", "'
                . $data['ID_MEMBER'] . '", "'
                . $data['ID_PROMO'] . '", "'
                . $data['PEG_ID_USER'] . '", "'
                . $data['ID_PEGAWAI'] . '", "'
                . $data['JUMLAH_DEPOSIT_UANG'] . '", "'
                . $data['BONUS_DEPOSIT_UANG'] . '", "'
                . date("Y-m-d")
                . '");'
            );
        return $this->respondCreated($data);
    }
    public function postDelete()
    {
        $data = $this->request->getPost();
        $this->db->query(
            'UPDATE deposit_uangs
            SET IS_DELETED_DEPOSIT_UANG = 1
            WHERE ID_DEPOSIT_UANG = "' . $data['ID_DEPOSIT_UANG'] . '";'
        );
        return $this->respondDeleted($data);
    }
}