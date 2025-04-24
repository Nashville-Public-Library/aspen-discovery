package com.turning_leaf_technologies.hoopla;

class HooplaTitle {
	private final long id;
	private final long hooplaId;
	private final long checksum;
	private final boolean active;
	private final long rawResponseLength;
	private final String hooplaType;
	private boolean foundInExport;

	HooplaTitle(long id, long hooplaId, long checksum, boolean active, long rawResponseLength, String hooplaType) {
		this.id = id;
		this.hooplaId = hooplaId;
		this.checksum = checksum;
		this.active = active;
		this.rawResponseLength = rawResponseLength;
		this.hooplaType = hooplaType != null ? hooplaType : "Instant";
	}

	long getId() {
		return id;
	}

	long getHooplaId() {
		return hooplaId;
	}

	long getChecksum() {
		return checksum;
	}

	boolean isActive() {
		return active;
	}

	long getRawResponseLength() {
	    return rawResponseLength;
    }

	public boolean isFoundInExport() {
		return foundInExport;
	}

	public void setFoundInExport(boolean foundInExport) {
		this.foundInExport = foundInExport;
	}

	public String getHooplaType() {
		return hooplaType;
	}
}