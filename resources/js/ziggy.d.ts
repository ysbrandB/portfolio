/* This file is generated by Ziggy. */
declare module 'ziggy-js' {
  interface RouteList {
    "sanctum.csrf-cookie": [],
    "ignition.healthCheck": [],
    "ignition.executeSolution": [],
    "ignition.updateConfig": [],
    "items.index": [],
    "items.create": [],
    "items.store": [],
    "items.edit": [
        {
            "name": "item",
            "required": true
        }
    ],
    "items.update": [
        {
            "name": "item",
            "required": true
        }
    ],
    "items.destroy": [
        {
            "name": "item",
            "required": true,
            "binding": "id"
        }
    ],
    "items.show": [
        {
            "name": "public_id",
            "required": true
        }
    ],
    "attribute_types.index": [],
    "attribute_types.create": [],
    "attribute_types.store": [],
    "attribute_types.show": [
        {
            "name": "attribute_type",
            "required": true
        }
    ],
    "attribute_types.edit": [
        {
            "name": "attribute_type",
            "required": true
        }
    ],
    "attribute_types.update": [
        {
            "name": "attribute_type",
            "required": true
        }
    ],
    "attribute_types.destroy": [
        {
            "name": "attribute_type",
            "required": true
        }
    ],
    "dashboard": [],
    "profile.edit": [],
    "profile.update": [],
    "profile.destroy": [],
    "register": [],
    "login": [],
    "password.request": [],
    "password.email": [],
    "password.reset": [
        {
            "name": "token",
            "required": true
        }
    ],
    "password.store": [],
    "verification.notice": [],
    "verification.verify": [
        {
            "name": "id",
            "required": true
        },
        {
            "name": "hash",
            "required": true
        }
    ],
    "verification.send": [],
    "password.confirm": [],
    "password.update": [],
    "logout": []
}
}
export {};
