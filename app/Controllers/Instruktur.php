<?php

namespace App\Controllers;

class Instruktur extends BaseController
{
    public function getIndex()
    {
        return $this->respond($this->db->query(
            'SELECT * FROM instrukturs NATURAL JOIN users WHERE IS_DELETED_INSTRUKTUR IS NULL AND IS_DELETED_USER IS NULL;'
            )->getResultArray(), 200);
    }
    public function postUpdate()
    {
        $data = $this->request->getJSON();
        $this->db->query(
            'UPDATE instrukturs I INNER JOIN users U ON U.ID_USER = I.ID_USER
            SET U.NAMA_USER = "' . $data->NAMA_USER . '",
                U.EMAIL_USER = "' . $data->EMAIL_USER . '",
                U.FOTO_USER = "' . $data->FOTO_USER . '",
                U.TANGGAL_LAHIR_USER = "' . $data->TANGGAL_LAHIR_USER . '",
                I.DESKRIPSI_INSTRUKTUR = "' . $data->DESKRIPSI_INSTRUKTUR . '"
            WHERE I.ID_INSTRUKTUR = "' . $data->ID_INSTRUKTUR . '" AND U.ID_USER = "' . $data->ID_USER . '";'
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
            'INSERT INTO instrukturs (
                ID_INSTRUKTUR,
                ID_USER,
                DESKRIPSI_INSTRUKTUR)
            VALUES ("'
                . $data->ID_INSTRUKTUR . '", "'
                . $data->ID_USER . '", "'
                . $data->DESKRIPSI_INSTRUKTUR
                . '");'
            );
        $this->db->transComplete();
        return $this->respondCreated($data);
    }
    public function postDelete()
    {
        $data = $this->request->getJSON();
        $this->db->query(
            'UPDATE users U INNER JOIN instrukturs U ON U.ID_USER = I.ID_USER
            SET I.IS_DELETED_INSTRUKTUR = 1,
                U.IS_DELETED_USER = 1
            WHERE I.ID_INSTRUKTUR = "' . $data->ID_INSTRUKTUR . '" AND U.ID_USER = "' . $data->ID_USER . '";'
        );
        return $this->respondDeleted($data);
    }
    public function getGenerateID()
    {
        return $this->respond(['I' . sprintf("%02d", $this->db->query(
            'SELECT COUNT(ID_INSTRUKTUR)+1 FROM instrukturs;'
            )->getResultArray()[0]['COUNT(ID_INSTRUKTUR)+1'])], 200);
    }
}