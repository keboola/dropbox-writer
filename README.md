# Keboola Dropbox Writer

## Configuration

### Authentication

To generate an **api key** for Dropbox, use the [OAuth API](http://docs.oauth9.apiary.io/#reference/api/generate-oauth-token-for-oauth-10-applications/generate-token-from-a-web-form/ui), where the **api** parameter should be set to `wr-dropbox` and the **id** parameter to whatever you want to call your credentials, which will then be used in the **credentials** parameter in Dropbox configuration below.

### Configuration data

- **storage.input.tables**: `source` says which table to upload to `destination`. The Dropbox account is selected automatically by api key.
- **parameters.credentials**: ID of the OAuth generated credentials. Either **credentials** or **api_key** is required.
- **parameters.api_key**: Optionally, the api key can be set directly in the configuration, skipping the OAuth API key retrieval
- **parameters.mode**: Setting to `rewrite` will force the file to be at the path specified in **destination**. If left empty, or set to another value, and the destination file already exists, the new one is appended by a number suffix.

#### Example

		{
			"storage": {
				"input": {
					"tables": [
						{
							"source": "in.c-cloudsearch.delete",
							"destination": "delejte.csv"
						},
						{
							"source": "in.c-ex-api-zendesk-test.tickets",
							"destination": "vstupenky.csv"
						}
					]
				}
			},
			"parameters": {
					"credentials": "test",
					"mode": "rewrite"
			}
		}
