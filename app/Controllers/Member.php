<?php

namespace App\Controllers;

class Member extends BaseController
{
    public function getIndex()
    {
        return $this->respond($this->db->query(
            'SELECT * FROM members NATURAL JOIN users WHERE IS_DELETED_MEMBER IS NULL AND IS_DELETED_USER IS NULL;'
            )->getResultArray(), 200);
    }
    public function postUpdate()
    {
        $data = $this->request->getJSON();
        $this->db->query(
            'UPDATE members M INNER JOIN users U ON U.ID_USER = M.ID_USER
            SET U.NAMA_USER = "' . $data->NAMA_USER . '",
                U.EMAIL_USER = "' . $data->EMAIL_USER . '",
                U.FOTO_USER = "' . $data->FOTO_USER . '",
                U.TANGGAL_LAHIR_USER = "' . $data->TANGGAL_LAHIR_USER . '",
                M.ALAMAT_MEMBER = "' . $data->ALAMAT_MEMBER . '",
                M.TELEPON_MEMBER = "' . $data->TELEPON_MEMBER . '"
            WHERE M.ID_MEMBER = "' . $data->ID_MEMBER . '" AND U.ID_USER = "' . $data->ID_USER . '";'
        );
        return $this->respond($data, 200);
    }
    public function postCreate()
    {
        $data = $this->request->getJSON();
        $this->db->transStart();
        $this->db->query(
            'INSERT INTO users (
                ID_USER,
                NAMA_USER,
                TANGGAL_DIBUAT_USER,
                FOTO_USER,
                EMAIL_USER,
                PASSWORD_USER,
                TANGGAL_LAHIR_USER)
            VALUES ("'
                . $data->ID_USER . '", "'
                . $data->NAMA_USER . '", "'
                . date("Y-m-d") . '", "'
                . $data->FOTO_USER . '", "'
                . $data->EMAIL_USER . '", "'
                . password_hash($data->TANGGAL_LAHIR_USER, PASSWORD_BCRYPT) . '", "'
                . $data->TANGGAL_LAHIR_USER
                . '");');
        $this->db->query(
            'INSERT INTO members (
                ID_MEMBER,
                ID_USER,
                ALAMAT_MEMBER,
                TELEPON_MEMBER,
                SISA_DEPOSIT_MEMBER)
            VALUES ("'
                . $data->ID_MEMBER . '", "'
                . $data->ID_USER . '", "'
                . $data->ALAMAT_MEMBER . '", "'
                . $data->TELEPON_MEMBER . '", "
                0
                ");'
            );
        $this->db->transComplete();
        return $this->respondCreated($data);
    }
    public function postDelete()
    {
        $data = $this->request->getJSON();
        $this->db->query(
            'UPDATE users U INNER JOIN members M ON U.ID_USER = M.ID_USER
            SET M.IS_DELETED_MEMBER = 1,
                U.IS_DELETED_USER = 1
            WHERE M.ID_MEMBER = "' . $data->ID_MEMBER . '" AND U.ID_USER = "' . $data->ID_USER . '";'
        );
        return $this->respondDeleted($data);
    }
    public function getGenerateID()
    {
        return $this->respond([date("y") . '.' . date("m") . '.' . sprintf("%03d", $this->db->query(
            'SELECT COUNT(ID_MEMBER)+1 FROM members
            WHERE ID_MEMBER LIKE "' . date("y") . '.' . date("m") . '.%";'
            )->getResultArray()[0]['COUNT(ID_MEMBER)+1'])], 200);
    }
}