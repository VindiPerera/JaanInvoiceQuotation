UPDATE quote_templates
SET hardware_items = JSON_ARRAY(
  JSON_OBJECT(
    'item_name', '2D Barcode Reading Scanner',
    'description', '2D barcode reading capability
Desktop stand included
USB connectivity (plug and play)',
    'warranty', NULL,
    'quantity', '1',
    'unit_price', '10000'
  ),
  JSON_OBJECT(
    'item_name', 'i5 4th Gen PC',
    'description', 'i5 4th gen
8gb ram
19in monitor',
    'warranty', '1 Year',
    'quantity', '1',
    'unit_price', '125000'
  ),
  JSON_OBJECT(
    'item_name', 'Commercial Grade Cash Drawer',
    'description', 'Commercial grade construction
Multi-compartment design for efficient cash management
Compatible with receipt printer (auto-open)
Lock and key included for security',
    'warranty', NULL,
    'quantity', '1',
    'unit_price', '70000'
  )
)
WHERE id = 1;

SELECT hardware_items FROM quote_templates WHERE id = 1;
