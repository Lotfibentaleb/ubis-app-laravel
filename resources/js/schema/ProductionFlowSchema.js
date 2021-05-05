export default {
  schema: {
    "$schema": "http://json-schema.org/draft-07/schema#",
    "$id": "https://example.com/object1620143319.json",
    "definitions": {},
    "title": "Root",
    "type": "array",
    "default": [],
    "uniqueItems":true,
    "items":{
      "$id": "#root/items",
      "title": "Items",
      "type": "object",
      "required": [
        "required",
        "step_name",
        "production_section_template",
      ],
      "additionalProperties": false,
      "properties": {
        "required": {
          "$id": "#root/items/required",
          "title": "required",
          "type": "boolean",
          "examples": [
            false
          ],
          "default": false
        },
        "step_name": {
          "$id": "#root/items/step_name",
          "title": "step_name",
          "type": "string",
          "description" : "step name",
          "examples": [
            "visual.test.1"
          ],
          "default": ""
        },
        "production_section_template": {
          "$id": "#root/items/production_section_template",
          "title": "production_section_template",
          "type": "number",
          "examples": [
            2
          ],
          "default": 1
        },
      }
    }
  }
}