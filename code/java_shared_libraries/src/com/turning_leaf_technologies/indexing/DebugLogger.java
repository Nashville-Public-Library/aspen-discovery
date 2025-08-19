package com.turning_leaf_technologies.indexing;

/**
 * Interface for objects that can log debug messages during indexing operations.
 * This allows the shared indexing libraries to log debug information without
 * creating circular dependencies on the reindexer module.
 */
public interface DebugLogger {
	/**
	 * Add a debug message if debug logging is enabled.
	 * @param message The debug message to log.
	 * @param level The indentation level (1-5, with 5 being the greatest indentation).
	 */
	void addDebugMessage(String message, int level);
	
	/**
	 * Check if debug logging is enabled.
	 * @return True if debug logging is enabled, false otherwise.
	 */
	boolean isDebugEnabled();
}