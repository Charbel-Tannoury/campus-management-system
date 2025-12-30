package models;

/**
 * Student Statement/Conversation Model
 * 
 * Represents a conversation thread between a student and faculty.
 * Each student_statment record creates a chat/messaging thread that can contain
 * multiple messages (stored in ss_messages table).
 * 
 * Purpose:
 * - Creates a conversation context for student-faculty communication
 * - Used in the messaging/requests system
 * - Links students to faculty for direct communication
 * 
 * Database Table: student_statment
 * 
 * Related Tables:
 * - ss_messages: Contains actual messages in the conversation
 * - students: The student who initiated the conversation
 * - university: The faculty being contacted
 * 
 * Use Cases:
 * - Student requests/inquiries to faculty administration
 * - Academic advising conversations
 * - Administrative support chat threads
 */
public class student_statment {
    private int ss_id;          // Statement/conversation ID (primary key)
    private int faculty_id;     // Faculty being contacted
    private int student_id;     // Student who initiated conversation
    private int created_at;     // Timestamp when conversation started
    public student_statment(int ss_id, int faculty_id, int student_id, int created_at) {
        this.ss_id = ss_id;
        this.faculty_id = faculty_id;
        this.student_id = student_id;
        this.created_at = created_at;
    }
    public int getSs_id() {
        return ss_id;
    }
    public int getFaculty_id() {
        return faculty_id;
    }
    public int getStudent_id() {
        return student_id;
    }
    public int getCreated_at() {
        return created_at;
    }
}