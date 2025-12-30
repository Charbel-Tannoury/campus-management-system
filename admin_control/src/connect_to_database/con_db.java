package connect_to_database;

import java.sql.*;
import javax.sql.rowset.CachedRowSet;
import javax.sql.rowset.RowSetProvider;

/**
 * Database connection handler that runs SQL queries in a separate thread.
 * Supports both SELECT queries (returns ResultSet) and UPDATE/INSERT/DELETE operations.
 * Uses CachedRowSet for disconnected result sets.
 */
public class con_db extends Thread {
    // Database connection parameters
    private String url = "jdbc:mysql://localhost:3306/campus-management-system";
    private String user = "root";
    private String password = "";
    
    // SQL query to execute
    private String sql;
    
    // Cached result set for SELECT queries
    private CachedRowSet result;
    
    // Number of rows affected by UPDATE/INSERT/DELETE operations
    private int updateCount = 0;
    
    // Flag to determine if this is an update operation or a query
    private boolean isUpdate = false;

    /**
     * Constructor that initializes the database thread with a SQL query.
     * Automatically detects if the query is an update operation (INSERT/UPDATE/DELETE).
     * 
     * @param sql The SQL query to execute
     */
    public con_db(String sql){
        this.sql = sql;
        String trimmedSql = sql.trim().toUpperCase();
        // Determine query type based on SQL command
        this.isUpdate = trimmedSql.startsWith("INSERT") || 
                       trimmedSql.startsWith("UPDATE") || 
                       trimmedSql.startsWith("DELETE");
    }

    /**
     * Thread execution method that runs the SQL query.
     * For UPDATE operations: executes and stores affected row count.
     * For SELECT operations: executes query and stores results in CachedRowSet.
     */
    public void run() {
        try (Connection conn = DriverManager.getConnection(url, user, password)) {
            if (isUpdate) {
                // Execute UPDATE/INSERT/DELETE and get affected row count
                try (Statement stmt = conn.createStatement()) {
                    updateCount = stmt.executeUpdate(sql);
                }
            } else {
                // Execute SELECT query and cache results for disconnected use
                try (Statement stmt = conn.createStatement();
                     ResultSet rs = stmt.executeQuery(sql)) {
                    // Create cached row set to allow result access after connection closes
                    CachedRowSet crs = RowSetProvider.newFactory().createCachedRowSet();
                    crs.populate(rs);
                    result = crs;
                }
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    /**
     * Gets the cached result set from a SELECT query.
     * @return ResultSet containing query results, or null if query hasn't executed yet
     */
    public ResultSet getResult() {
        return result;
    }
    
    /**
     * Gets the number of rows affected by an UPDATE/INSERT/DELETE operation.
     * @return Number of affected rows
     */
    public int getUpdateCount() {
        return updateCount;
    }
    
    /**
     * Creates a new database connection.
     * @return Active database connection
     * @throws SQLException if connection fails
     */
    public Connection getConnection() throws SQLException {
        return DriverManager.getConnection(url, user, password);
    }
}
