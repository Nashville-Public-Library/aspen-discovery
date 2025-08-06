package com.turning_leaf_technologies.indexing;

import com.turning_leaf_technologies.marc.MarcUtil;

import java.util.HashMap;
import java.util.Objects;
import java.util.Set;
import java.util.TreeSet;
import java.util.regex.Pattern;

class InclusionRule {
	private final String recordType;
	private final boolean matchAllLocations;
	private boolean isLocationExactMatch;
	private String locationCodeToMatch;
	private Pattern locationCodePattern;
	private Pattern locationsToExcludePattern = null;

	private final boolean matchAllSubLocations;
	private final Pattern subLocationCodePattern;
	private Pattern subLocationsToExcludePattern = null;

	private boolean matchAlliTypes = false;
	private final Pattern iTypePattern;
	private Pattern iTypesToExcludePattern = null;

	private boolean matchAllAudiences = false;
	private final Pattern audiencePattern;
	private Pattern audiencesToExcludePattern = null;

	private boolean matchAllFormats = false;
	private final Pattern formatPattern;
	private Pattern formatsToExcludePattern = null;

	private boolean matchAllShelfLocations = false;
	private final Pattern shelfLocationPattern;
	private Pattern shelfLocationsToExcludePattern = null;

	private boolean matchAllCollectionCodes = false;
	private final Pattern collectionCodePattern;
	private Pattern collectionCodesToExcludePattern = null;

	private final boolean includeHoldableOnly;
	private final boolean includeItemsOnOrder;
	private final boolean includeEContent;
	private final String marcTagToMatch;
	private final Pattern marcValueToMatchPattern;
	private final boolean includeExcludeMatches;
	private final String urlToMatch;
	private final String urlReplacement;
	private final String locationPatternString;
	private final String formatPatternString;

	private static final Pattern isRegexPattern = Pattern.compile("[.*?{}\\\\^\\[\\]|$]");
	InclusionRule(String recordType, String locationCode, String subLocationCode, String locationsToExclude, String subLocationsToExclude, String iType, String iTypesToExclude, String audience, String audiencesToExclude,String format, String formatsToExclude, String shelfLocation, String shelfLocationsToExclude, String collectionCode, String collectionCodesToExclude, boolean includeHoldableOnly, boolean includeItemsOnOrder, boolean includeEContent, String marcTagToMatch, String marcValueToMatch, boolean includeExcludeMatches, String urlToMatch, String urlReplacement){
		this.recordType = recordType;
		this.includeHoldableOnly = includeHoldableOnly;
		this.includeItemsOnOrder = includeItemsOnOrder;
		this.includeEContent = includeEContent;

		//Location & Sublocation Code Inclusion/Exclusion Check
		if (locationCode.isEmpty()){
			locationCode = ".*";
		}
		this.locationPatternString = locationCode;
		matchAllLocations = locationCode.equals(".*");
		if (!matchAllLocations){
			if (isRegexPattern.matcher(locationCode).find()) {
				this.locationCodePattern = Pattern.compile(locationCode, Pattern.CASE_INSENSITIVE);
			}else{
				this.locationCodeToMatch = locationCode;
				isLocationExactMatch = true;
			}
		}
		if (subLocationCode.isEmpty()){
			subLocationCode = ".*";
		}
		matchAllSubLocations = subLocationCode.equals(".*");
		this.subLocationCodePattern = Pattern.compile(subLocationCode, Pattern.CASE_INSENSITIVE);

		if (locationsToExclude != null && !locationsToExclude.isEmpty()){
			this.locationsToExcludePattern = Pattern.compile(locationsToExclude, Pattern.CASE_INSENSITIVE);
		}
		if (subLocationsToExclude != null && !subLocationsToExclude.isEmpty()){
			this.subLocationsToExcludePattern = Pattern.compile(subLocationsToExclude, Pattern.CASE_INSENSITIVE);
		}

		//iType Inclusion/Exclusion Check
		if (iType == null || iType.isEmpty()){
			iType = ".*";
		}
		if (iTypesToExclude == null) {
			iTypesToExclude = "";
		}
		if (iType.equals(".*") && (iTypesToExclude.isEmpty())){
			matchAlliTypes = true;
		}
		this.iTypePattern = Pattern.compile(iType, Pattern.CASE_INSENSITIVE);
		if (!iTypesToExclude.isEmpty()){
			this.iTypesToExcludePattern = Pattern.compile(iTypesToExclude, Pattern.CASE_INSENSITIVE);
		}

		//Audience Inclusion/Exclusion Check
		if (audience == null || audience.isEmpty()) {
			audience = ".*";
		}
		if (audiencesToExclude == null) {
			audiencesToExclude = "";
		}
		if (audience.equals(".*") && audiencesToExclude.isEmpty()){
			matchAllAudiences = true;
		}
		this.audiencePattern = Pattern.compile(audience, Pattern.CASE_INSENSITIVE);
		if (audiencesToExclude != null && !audiencesToExclude.isEmpty()){
			this.audiencesToExcludePattern = Pattern.compile(audiencesToExclude, Pattern.CASE_INSENSITIVE);
		}

		//Format Inclusion/Exclusion Check
		if (format == null || format.isEmpty()){
			format = ".*";
		}
		this.formatPatternString = format;
		if (formatsToExclude == null) {
			formatsToExclude = "";
		}
		if (format.equals(".*") && formatsToExclude.isEmpty()){
			matchAllFormats = true;
		}
		this.formatPattern = Pattern.compile(format, Pattern.CASE_INSENSITIVE);
		if (!formatsToExclude.isEmpty()){
			this.formatsToExcludePattern = Pattern.compile(formatsToExclude, Pattern.CASE_INSENSITIVE);
		}

		//Shelf Location Inclusion/Exclusion Check
		if (shelfLocation == null || shelfLocation.isEmpty()) {
			shelfLocation = ".*";
		}
		if (shelfLocation.equals(".*") && (shelfLocationsToExclude == null || shelfLocationsToExclude.isEmpty())){
			matchAllShelfLocations = true;
		}
		this.shelfLocationPattern = Pattern.compile(shelfLocation, Pattern.CASE_INSENSITIVE);
		if (shelfLocationsToExclude != null && !shelfLocationsToExclude.isEmpty()){
			this.shelfLocationsToExcludePattern = Pattern.compile(shelfLocationsToExclude, Pattern.CASE_INSENSITIVE);
		}

		//Collection Code Inclusion/Exclusion Check
		if (collectionCode == null || collectionCode.isEmpty()) {
			collectionCode = ".*";
		}
		if (collectionCode.equals(".*") && collectionCodesToExclude.isEmpty()){
			matchAllCollectionCodes = true;
		}
		this.collectionCodePattern = Pattern.compile(collectionCode, Pattern.CASE_INSENSITIVE);
		if (!collectionCodesToExclude.isEmpty()){
			this.collectionCodesToExcludePattern = Pattern.compile(collectionCodesToExclude, Pattern.CASE_INSENSITIVE);
		}
		this.marcTagToMatch = Objects.requireNonNullElse(marcTagToMatch, "");

		if (marcValueToMatch == null || marcValueToMatch.isEmpty()){
			marcValueToMatch = ".*";
		}
		this.marcValueToMatchPattern = Pattern.compile(marcValueToMatch);

		this.includeExcludeMatches = includeExcludeMatches;

		this.urlToMatch = urlToMatch;
		this.urlReplacement = urlReplacement;
	}

	private String lastIdentifier = null;
	private boolean lastIdentifierResult = false;

	HashMap<String, Boolean> inclusionCache = new HashMap<>();

	//TODO: We can potentially just pass in the ItemInfo object instead of all or most of these parameters
	//		This would likely require creating an interface for ItemInfo under java_shared_libraries.
	boolean isItemIncluded(String itemIdentifier, String recordType, String locationCode, String subLocationCode, String iType, TreeSet<String> audiences, String audiencesAsString, String format, String shelfLocation, String collectionCode, boolean isHoldable, boolean isOnOrder, boolean isEContent, org.marc4j.marc.Record marcRecord, DebugLogger debugLogger){
		if (lastIdentifier != null && lastIdentifier.equals(itemIdentifier)){
			return lastIdentifierResult;
		}

		lastIdentifier = itemIdentifier;
		//Do the quick checks first
		if (!isEContent && (includeHoldableOnly && !isHoldable)) {
			if (debugLogger != null && debugLogger.isDebugEnabled()) {
				debugLogger.addDebugMessage("Item " + itemIdentifier + " excluded from scope because 'Include Holdable Only' is enabled but item is not holdable (holdable=" + isHoldable + ", format=" + format + ")", 3);
			}
			lastIdentifierResult = false;
			return false;
		} else if (!includeItemsOnOrder && isOnOrder){
			if (debugLogger != null && debugLogger.isDebugEnabled()) {
				debugLogger.addDebugMessage("Item " + itemIdentifier + " excluded from scope because 'Include Items On Order' is disabled but item is on order", 3);
			}
			lastIdentifierResult = false;
			return  false;
		} else if (!includeEContent && isEContent){
			if (debugLogger != null && debugLogger.isDebugEnabled()) {
				debugLogger.addDebugMessage("Item " + itemIdentifier + " excluded from scope because 'Include eContent' is disabled but item is eContent", 3);
			}
			lastIdentifierResult = false;
			return  false;
		} else if (!this.recordType.equals(recordType)) {
			if (debugLogger != null && debugLogger.isDebugEnabled()) {
				debugLogger.addDebugMessage("Item " + itemIdentifier + " excluded from scope because record type '" + recordType + "' does not match rule record type '" + this.recordType + "'", 3);
			}
			lastIdentifierResult = false;
			return  false;
		}

		//Determine if we have already determined this already
		boolean hasCachedValue = true;
		if (locationCode == null){
			if (matchAllLocations){
				locationCode = "null";
			}else {
				lastIdentifierResult = false;
				return false;
			}
		}

		if (matchAlliTypes){
			iType = "any";
		}
		String audienceKey = audiencesAsString;
		if (matchAllAudiences){
			audienceKey = "all";
		}
		if (matchAllFormats){
			format = "any";
		}
		if(matchAllShelfLocations){
			shelfLocation = "all";
		}
		if(matchAllCollectionCodes){
			collectionCode = "any";
		}
		String inclusionCacheKey = locationCode + subLocationCode + iType + audienceKey + format + shelfLocation + collectionCode;
		Boolean cachedInclusion = inclusionCache.get(inclusionCacheKey);
		if (cachedInclusion == null){
			hasCachedValue = false;
		}

		boolean isIncluded;

		if (!hasCachedValue){
			isIncluded = true;
			if (!matchAllLocations) {
				if (isLocationExactMatch) {
					if (!locationCodeToMatch.equalsIgnoreCase(locationCode)) {
						if (debugLogger != null && debugLogger.isDebugEnabled()) {
							debugLogger.addDebugMessage("Item " + itemIdentifier + " excluded from scope because location '" + locationCode + "' does not match required location '" + locationCodeToMatch + "'", 3);
						}
						isIncluded = false;
					}
				} else {
					if (!locationCodePattern.matcher(locationCode).matches()) {
						if (debugLogger != null && debugLogger.isDebugEnabled()) {
							debugLogger.addDebugMessage("Item " + itemIdentifier + " excluded from scope because location '" + locationCode + "' does not match pattern '" + locationPatternString + "'", 3);
						}
						isIncluded = false;
					}
				}
			}
			if (isIncluded && !locationCode.isEmpty() && locationsToExcludePattern != null) {
				if (locationsToExcludePattern.matcher(locationCode).matches()) {
					if (debugLogger != null && debugLogger.isDebugEnabled()) {
						debugLogger.addDebugMessage("Item " + itemIdentifier + " excluded from scope because location '" + locationCode + "' matches exclusion pattern", 3);
					}
					isIncluded = false;
				}
			}

			if (isIncluded && !subLocationCode.isEmpty()){
				if (!matchAllSubLocations) {
					if (!subLocationCodePattern.matcher(subLocationCode).matches()) {
						isIncluded = false;
					}
					if (isIncluded && subLocationsToExcludePattern != null) {
						if (subLocationsToExcludePattern.matcher(subLocationCode).matches()){
							isIncluded = false;
						}
					}
				}
			}

			//Check Formats to include & exclude
			if (isIncluded && format != null && !format.isEmpty()){
				if (!matchAllFormats) {
					if (!formatPattern.matcher(format).matches()) {
						if (debugLogger != null && debugLogger.isDebugEnabled()) {
							debugLogger.addDebugMessage("Item " + itemIdentifier + " excluded from scope because format '" + format + "' does not match pattern '" + formatPatternString + "'", 3);
						}
						isIncluded = false;
					}
					if (isIncluded && formatsToExcludePattern != null) {
						if(formatsToExcludePattern.matcher(format).matches()){
							if (debugLogger != null && debugLogger.isDebugEnabled()) {
								debugLogger.addDebugMessage("Item " + itemIdentifier + " excluded from scope because format '" + format + "' matches exclusion pattern", 3);
							}
							isIncluded = false;
						}
					}
				}
			}

			//Check iTypes to include & exclude
			if (isIncluded && iType != null){
				if (!matchAlliTypes) {
					if (!iTypePattern.matcher(iType).matches()) {
						isIncluded = false;
					}
					if (isIncluded && iTypesToExcludePattern != null) {
						if(iTypesToExcludePattern.matcher(iType).matches()) {
							isIncluded = false;
						}
					}
				}
			}
			//Check Shelf Location to include & exclude
			if (isIncluded && !matchAllShelfLocations){ //still want to process empty shelf locations, don't check for length > 0
				if (shelfLocation != null && !shelfLocation.isEmpty()){
					if (!shelfLocationPattern.matcher(shelfLocation).matches()) {
						isIncluded = false;
					}
					if (isIncluded && shelfLocationsToExcludePattern != null) {
						if(shelfLocationsToExcludePattern.matcher(shelfLocation).matches()) {
							isIncluded = false;
						}
					}
				}
				else {
					if (!shelfLocationPattern.pattern().equals(".*")) {
						isIncluded = false;
					}
				}

			}
			//Check Collection Code to include & exclude
			if (isIncluded && !matchAllCollectionCodes){
				if (collectionCode != null && !collectionCode.isEmpty()) {
					if (!collectionCodePattern.matcher(collectionCode).matches()) {
						isIncluded = false;
					}
					if (isIncluded && collectionCodesToExcludePattern != null) {
						if(collectionCodesToExcludePattern.matcher(collectionCode).matches()) {
							isIncluded = false;
						}
					}
				}
				else {
					if (!collectionCodePattern.pattern().equals(".*")) {
						isIncluded = false;
					}
				}
			}
			//Check audiences to include & exclude
			if (isIncluded && !matchAllAudiences){
				boolean audienceMatched = false;
				for (String audience : audiences) {
					//As soon as something is either matched or excluded we can stop checking.
					if (audiencePattern.matcher(audience).matches()) {
						audienceMatched = true;
						break;
					}
				}
				if (audienceMatched){
					for (String audience : audiences) {
						//As soon as something is either matched or excluded we can stop checking.
						if (audiencesToExcludePattern != null && audiencesToExcludePattern.matcher(audience).matches()) {
							audienceMatched = false;
							break;
						}
					}
				}
				isIncluded = audienceMatched;
			}

			//Make sure not to cache marc tag determination
			inclusionCache.put(inclusionCacheKey, isIncluded);
		}else{
			isIncluded = cachedInclusion;
		}
		//Make sure not to cache marc tag determination
		//TODO: *Someday* if the marc tag to match is the item tag, only get the marc tag for the item we are on.
		if (isIncluded && !marcTagToMatch.isEmpty()) {
			boolean hasMatch = false;
			Set<String> marcValuesToCheck = MarcUtil.getFieldList(marcRecord, marcTagToMatch);
			for (String marcValueToCheck : marcValuesToCheck) {
				if (marcValueToMatchPattern.matcher(marcValueToCheck).matches()) {
					hasMatch = true;
					break;
				}
			}
			isIncluded = hasMatch && includeExcludeMatches;
		}

		if (debugLogger != null && debugLogger.isDebugEnabled()) {
			if (isIncluded) {
				debugLogger.addDebugMessage("Item " + itemIdentifier + " included in scope (location='" + locationCode + "', format='" + format + "', holdable=" + isHoldable + ")", 3);
			}
		}

		lastIdentifierResult = isIncluded;
		return isIncluded;
	}

	String getLocalUrl(String url){
		if (urlToMatch == null || urlToMatch.isEmpty() || urlReplacement == null || urlReplacement.isEmpty()){
			return url;
		}else{
			return url.replaceFirst(urlToMatch, urlReplacement);
		}
	}
}
