# PHP-PDO-MySQL
<hr>
<h3>รวม Class ที่เคยสร้างไว้ใช้กับโปรเจคส่วนตัว</h3>
ประกอบไปด้วย Function ที่สำคัญดังนี้
<ol>
  <li>ConnectMySQL</li>
  <li>ExecuteDML ใช้กับ Insert และ Update และมี rollback ที่รองรับกับ Storage Engine "InnoDB"</li>  
  <li>PrepareDML ใช้ร่วมกับสร้าง Attibute สำหรับรับค่าข้อมูล Ex. insert into user (username,password,status) values (:un,:pw,:stas)</li>
  <li>StoreProcedure เหมาะกับการทำงานร่วมกับ Stored Procedure in MySQL </li>
  <li>QueryFetchAllNum, QueryFetchAllNumByPrepare, QueryFetchAllAssoc, QueryFetchAllAssocByPrepare นำไปใช้ร่วมกับ Select เพื่อดึงค่าทั้งหมด</li>
  <li>QueryFetchNum, QueryFetchNumByPrepare, QueryFetchAssoc, QueryFetchAssocByPrepare นำไปใช้ร่วมกับ Select เพื่อดึงค่าแค่ 1 ตัว</li>
  <li>Pagination ฟังก์ชั่นสำหรับการเลื่อนหน้าข้อมูล Ex. << | 1 | 2 | 3 | >> </li>
</ol>
