package models;

/**
 * Model class representing student grades for a course enrollment.
 * Tracks different assessment components: project, midterm, and final exams.
 */
public class grades {
    // Unique identifier for this grade record
    private int grade_id;
    // Reference to the enrollment record
    private int enrol_id;
    // Project assessment grade
    private int project_grade;
    // Midterm exam grade
    private int mid_grade;
    // First final exam grade
    private int final_final;
    // Second final exam grade (for retakes)
    private int second_final;
    
    /**
     * Constructor to create a grade record.
     * @param grade_id Unique grade record identifier
     * @param enrol_id Enrollment ID this grade belongs to
     * @param project_grade Project score
     * @param mid_grade Midterm exam score
     * @param final_final First final exam score
     * @param second_final Second final exam score (retake)
     */
    public grades(int grade_id, int enrol_id, int project_grade, int mid_grade, int final_final,int second_final) {
    this.grade_id = grade_id;
    this.enrol_id = enrol_id;
    this.project_grade = project_grade;
    this.mid_grade = mid_grade;
    this.final_final = final_final;
    this.second_final = second_final;
}

// Getter methods for accessing grade information

public int getGrade_id() {
    return grade_id;
}

public int getEnrol_id() {
    return enrol_id;
}

public int getProject_grade() {
    return project_grade;
}

public int getMid_grade() {
    return mid_grade;
}

public int getFinal_final() {
    return final_final;
}

public int getSecond_final() {
    return second_final;
}

// Setter methods for updating grade values

public void setProject_grade(int project_grade) {
    this.project_grade = project_grade;
}

public void setMid_grade(int mid_grade) {
    this.mid_grade = mid_grade;
}

public void setFinal_final(int final_final) {
    this.final_final = final_final;
}

public void setSecond_final(int second_final) {
    this.second_final = second_final;
}
}