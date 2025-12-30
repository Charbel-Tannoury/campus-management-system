package models;

/**
 * Model class representing a course in the university system.
 * Contains course identification, name, and detailed description.
 */
public class courses {
    // Unique identifier for the course
    private String course_id;
    // Name/title of the course
    private String course_name;
    // Detailed description of course content and requirements
    private String course_details;
    
    /**
     * Constructor to create a course instance.
     * @param courseId Unique course identifier
     * @param course_name Name of the course
     * @param course_details Course description and details
     */
    public courses(String courseId, String course_name, String course_details) {
    this.course_id = courseId;
    this.course_name = course_name;
    this.course_details = course_details;
}

// Getter and Setter methods with standard JavaBean naming conventions

public String getCourse_id() {
    return course_id;
}

public String getCourse_name() {
    return course_name;
}

public String getCourse_details() {
    return course_details;
}

public void setCourse_details(String course_details) {
    this.course_details = course_details;
}

public void setCourse_name(String course_name) {
    this.course_name = course_name;
}

public void setCourse_id(String course_id) {
    this.course_id = course_id;
}
}
