package models;

/**
 * Student Statement Messages Model
 * 
 * Represents individual messages within a student-faculty conversation.
 * Each message belongs to a student_statment (conversation thread).
 * 
 * Message Flow:
 * - from_id = 0: Message sent by faculty/admin
 * - from_id = student_id: Message sent by student
 * 
 * Features:
 * - Text messages with optional file attachments
 * - Bidirectional communication (student <-> faculty)
 * - Timestamp tracking
 * - File attachment support
 * 
 * Database Table: ss_messages
 * 
 * Related Tables:
 * - student_statment: Parent conversation thread
 * - students: Student participant (if from_id matches student_id)
 * 
 * Use Cases:
 * - Chat messages in student portal requests page
 * - Faculty responses to student inquiries
 * - Document sharing via attachments
 */
public class ss_messages {
private int message_id;     // Message ID (primary key)
private int ss_id;          // Conversation thread ID (foreign key to student_statment)
private int from_id;        // Sender ID (0=faculty, student_id=student)
private String message;     // Message text content
private String attach;      // Attachment filename (nullable)
private int created_at;     // Message timestamp
public ss_messages(int message_id, int ss_id, int from_id, String message, String attach, int created_at) {
    this.message_id = message_id;
    this.ss_id = ss_id;
    this.from_id = from_id;
    this.message = message;
    this.attach = attach;
    this.created_at = created_at;
}
public ss_messages(int ss_id, int from_id, String message, String attach) {
    this.ss_id = ss_id;
    this.from_id = from_id;
    this.message = message;
    this.attach = attach;}
public int getMessage_id() {
    return message_id;}
public int getSs_id() {
    return ss_id;}
public int getFrom_id() {
    return from_id;}
public String getMessage() {
    return message;}
public String getAttach() {
    return attach;}
public int getCreated_at() {
    return created_at;}
}
