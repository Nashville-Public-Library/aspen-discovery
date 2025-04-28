package org.aspen_discovery.reindexer;

import java.sql.ResultSet;
import java.sql.SQLException;

public class SavedRecordInfo {
	public long id;
	public long sourceId;
	public String recordIdentifier;
	public long groupedWorkId;
	public long editionId;
	public long audienceId;
	public long publisherId;
	public long publicationDateId;
	public long placeOfPublicationId;
	public long physicalDescriptionId;
	public long formatId;
	public long formatCategoryId;
	public long languageId;
	public boolean isClosedCaptioned;
	public boolean hasParentRecord;
	public boolean hasChildRecord;

	SavedRecordInfo(ResultSet getExistingRecordsForWorkRS) throws SQLException {
		id = getExistingRecordsForWorkRS.getLong("id");
		sourceId = getExistingRecordsForWorkRS.getLong("sourceId");
		groupedWorkId = getExistingRecordsForWorkRS.getLong("groupedWorkId");
		recordIdentifier = getExistingRecordsForWorkRS.getString("recordIdentifier");
		editionId = getExistingRecordsForWorkRS.getLong("editionId");
		publisherId = getExistingRecordsForWorkRS.getLong("publisherId");
		placeOfPublicationId = getExistingRecordsForWorkRS.getLong("placeOfPublicationId");
		publicationDateId = getExistingRecordsForWorkRS.getLong("publicationDateId");
		physicalDescriptionId = getExistingRecordsForWorkRS.getLong("physicalDescriptionId");
		formatId = getExistingRecordsForWorkRS.getLong("formatId");
		formatCategoryId = getExistingRecordsForWorkRS.getLong("formatCategoryId");
		languageId = getExistingRecordsForWorkRS.getLong("languageId");
		isClosedCaptioned = getExistingRecordsForWorkRS.getBoolean("isClosedCaptioned");
		hasParentRecord = getExistingRecordsForWorkRS.getBoolean("hasParentRecord");
		hasChildRecord = getExistingRecordsForWorkRS.getBoolean("hasChildRecord");
		audienceId = getExistingRecordsForWorkRS.getLong("audienceId");
	}

}
