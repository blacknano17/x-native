<?php
class Dbconfig5
{
    private $koneksi;
    private $db_dns, $db_user, $db_pass, $db_dns2, $mode;

    public function __construct($dbdns, $dbuser = null, $dbpass = null, $dbdns2 = null, $mode = null)
    {
        $this->db_dns = $dbdns;
        $this->db_user = $dbuser;
        $this->db_pass = $dbpass;
        $this->db_dns2 = $dbdns2;
        $this->mode = $mode;
    }

    function escape_str($str, $like = null)
    {
        $escaped_str = addslashes($str);
        if ($like) {
            $escaped_str = str_replace(['%', '_'], ['\\%', '\\_'], $escaped_str);
        }
        return $escaped_str;
    }

    private function bukaKoneksi()
    {
        if (!$this->koneksi) {
            try {
                $this->koneksi = new PDO($this->db_dns, $this->db_user, $this->db_pass, [
                    PDO::MYSQL_ATTR_FOUND_ROWS => true
                ]);
                $this->koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->koneksi->setAttribute(PDO::ATTR_AUTOCOMMIT, false); // Pastikan autocommit OFF
            } catch (PDOException $e) {
                error_log("Koneksi Gagal: " . $e->getMessage());
                throw new Exception("Koneksi Database Gagal.");
            }
        }
        return $this->koneksi;
    }

    private function tutupKoneksi()
    {
        $this->koneksi = null;
    }

    private function handleError($e)
    {
        error_log($e->getMessage());
        if ($this->mode === 'dev') {
            $msg = $e->getMessage();
            $msg = preg_replace('/\b(?:\d{1,3}\.){3}\d{1,3}\b/', '[REDACTED_IP]', $msg);
            $msg = preg_replace('/(host|dbname|database|db|user|username|password|pass)=[^;\s&"\'<>]+/i', '$1=***', $msg);
            $msg = preg_replace('/(user(?:name)?\s*[\'"])[^\'"]+([\'"])/i', '$1***$2', $msg);
            $msg = preg_replace('/(database|dbname|db)(\s*[\'"])[^\'"]+([\'"])/i', '$1$2***$3', $msg);
            return "Error: " . $msg;
        } else {
            return "Terjadi kesalahan pada query database.";
        }
    }

    public function start()
    {
        if (!$this->bukaKoneksi()->inTransaction()) {
            $this->bukaKoneksi()->beginTransaction();
        }
    }

    public function end()
    {
        try {
            if ($this->koneksi && $this->koneksi->inTransaction()) {
                $this->bukaKoneksi()->commit();
            }
        } catch (PDOException $e) {
            $this->close(); // Rollback jika commit gagal
            $this->handleError($e);
        }
        $this->tutupKoneksi();
    }

    /**
     * Commit variant untuk alur yang mempunyai artefak file. Berbeda dari end(),
     * kegagalan commit dilempar kembali agar caller dapat menghapus file yang
     * sudah dipindahkan dan tidak pernah mengirim respons sukses palsu.
     */
    public function endStrict()
    {
        try {
            if (!$this->bukaKoneksi()->commit()) {
                throw new PDOException('Commit transaksi gagal');
            }
        } catch (Throwable $e) {
            $this->close();
            throw $e;
        }
        $this->tutupKoneksi();
    }

    public function close()
    {
        if ($this->koneksi && $this->koneksi->inTransaction()) {
            $this->koneksi->rollBack();
        }
        $this->tutupKoneksi();
    }

    public function fetchObject($sql, $params = [], $className = 'stdClass')
    {
        $result = [];
        try {
            $db = $this->bukaKoneksi();
            $stmt = $db->prepare($sql);
            $stmt->setFetchMode(PDO::FETCH_CLASS, $className);
            $stmt->execute($params);
            $result = $stmt->fetchAll();
        } catch (PDOException $e) {
           return $this->handleError($e);
        }
        return $result;
    }

    public function fetchArray($sql, $params = [])
    {
        $result = [];
        try {
            $db = $this->bukaKoneksi();
            $stmt = $db->prepare($sql);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute($params);
            $result = $stmt->fetchAll();
        } catch (PDOException $e) {
           return $this->handleError($e);
        }
        return $result;
    }

     public function fetchObjectLow($sql,$params = [], $className = 'stdClass')
    {
        $result = [];
        try {
            $db = $this->bukaKoneksi();
            $stmt = $db->prepare($sql);
            $stmt->setFetchMode(PDO::FETCH_CLASS, $className);
            $stmt->execute($params);
            while ($row = $stmt->fetch()) {
                $result[] = $row;
            }
        } catch (PDOException $e) {
            return $this->handleError($e);
        }
        return $result;
    }

    public function fetchArrayLow($sql,$params = []){
        $result = [];
        try {
            $db = $this->bukaKoneksi();
            $stmt = $db->prepare($sql);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute($params);
            while ($row = $stmt->fetch()) {
                $result[] = $row;
            }
        } catch (PDOException $e) {
            return $this->handleError($e);
        }
        return $result;
    }

    public function fetchArrayIndex($sql, $idx = '', $params = [])
    {
        $result = [];
        try {
            $db = $this->bukaKoneksi();
            $stmt = $db->prepare($sql);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute($params);
            while ($row = $stmt->fetch()) {
                $result[$row[$idx]] = $row;
            }
        } catch (PDOException $e) {
           return $this->handleError($e);
        }
        return $result;
    }

      public function fetchObjectIndex($sql, $idx = '', $params = [], $className = 'stdClass')
    {
        $result = [];
        try {
            $db = $this->bukaKoneksi();
            $stmt = $db->prepare($sql);
            $stmt->setFetchMode(PDO::FETCH_CLASS, $className);
            $stmt->execute($params);
            while ($row = $stmt->fetch()) {
                $result[$row->$idx] = $row;
            }
        } catch (PDOException $e) {
            return $this->handleError($e);
        }
        return $result;
    }


   public function fetchColom($sql, $params = [])
    {
        $result = [];

        try {
            $db = $this->bukaKoneksi(); // Buka koneksi
            $stmt = $db->prepare($sql);  // Siapkan query
            $stmt->execute($params);     // Eksekusi query dengan parameter

            $result = $stmt->fetchColumn(); // Ambil data sebagai array asosiatif
            
        } catch (PDOException $e) {
           return $this->handleError($e);
        } 

        return $result;
    }

    public function eksekusi($sql, $params = [])
    {
        $response = [];
        try {
            $db = $this->bukaKoneksi();
            $stmt = $db->prepare($sql);
            $stmt->execute($params);

            $result = $stmt->rowCount();
            
            if($result === 0){
                return array('status'=>'failed','msg'=>'tidak ada row terpengaruh');
            }

            return array('status'=>'succes','msg'=>$result);


        } catch (PDOException $e) {
            if ($db->inTransaction()) {
                $this->close();
            }
           
            return array('status'=>'failed','msg'=>$this->handleError($e)); 
        }
        return $response;
    }

    public function lastInsertId()
    {
        return $this->bukaKoneksi()->lastInsertId();
    }


     public function fetchObjectStock($sql,$params = [] )
    {
       
        try {
            $db = $this->bukaKoneksi();
            $stmt = $db->prepare($sql);
          $stmt->execute($params);
             $stmt->setFetchMode(PDO::FETCH_CLASS,'stdClass');
            return $stmt;
            
        } catch (PDOException $e) {
            return $this->handleError($e);
        }
        
    
    }
}
