package models;

/**
 * Mails Data Model
 * 
 * Represents an email/announcement message in the university system.
 * Used for internal communications between administration and students/staff.
 * 
 * Database Table: mails
 * Fields:
 * - mail_id: Primary key, unique message identifier
 * - faculty_id: ID of faculty sending the message
 * - receivers: Target audience (0=all, 1=students, 2=professors, etc.)
 * - priority: Message importance level (low, medium, high, urgent)
 * - related_faculties: Bitmask or ID indicating which faculties should see this
 * - mail_title: Subject line of the message
 * - mail_info: Message body content
 * - attached_doc: Path/URL to attached document (if any)
 * - created_at: Timestamp when message was created
 * 
 * Two Constructors:
 * 1. Without created_at (for creating new messages)
 * 2. With created_at (for loading existing messages from database)
 */
public class mails {
private int mail_id;
private int faculty_id;
private int receivers;
private String priority;
private int related_faculties;
private String mail_title;
private String mail_info;
private String created_at;
private String attached_doc;
public mails(int mail_id, int faculty_id, String priority, int receivers, int related_faculties, String mail_title, String mail_info, String attached_doc) {
    this.mail_id = mail_id;
    this.faculty_id = faculty_id;
    this.receivers = receivers;
    this.priority = priority;
    this.related_faculties = related_faculties;
    this.mail_title = mail_title;
    this.mail_info = mail_info;
    this.attached_doc = attached_doc;
}
public mails(int mail_id, int faculty_id, int receivers, String priority, int related_faculties, String mail_title, String mail_info,String attached_doc,String created_at) {
    this.mail_id = mail_id;
    this.faculty_id = faculty_id;
    this.receivers = receivers;
    this.priority = priority;
    this.related_faculties = related_faculties;
    this.mail_title = mail_title;
    this.mail_info = mail_info;
    this.attached_doc = attached_doc;
    this.created_at = created_at;
}
public int getMail_id() {
    return mail_id;
}
public int getFaculty_id() {
    return faculty_id;
}
public int getReceivers() {
    return receivers;
}
public String getPriority() {
    return priority;
}
public int getRelated_faculties() {
    return related_faculties;
}
public String getMail_title() {
    return mail_title;
}
public String getMail_info() {
    return mail_info;
}
public String getCreated_at() {
    return created_at;
}
public String getAttached_doc() {
    return attached_doc;
}
public void setAttached_doc(String attached_doc) {
    this.attached_doc = attached_doc;
}
public void setMail_info(String mail_info) {
    this.mail_info = mail_info;
}
public void setMail_title(String mail_title) {
    this.mail_title = mail_title;
}
public void setRelated_faculties(int related_faculties) {
    this.related_faculties = related_faculties;
}
public void setPriority(String priority) {
    this.priority = priority;
}
public void setReceivers(int receivers) {
    this.receivers = receivers;
}
}
