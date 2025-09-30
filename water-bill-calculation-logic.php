<?php

/**
 * Water Bill Calculation Logic Explanation
 */

echo "💧 Water Bill Calculation Logic\n";
echo "===============================\n\n";

echo "📋 TIERED PRICING STRUCTURE:\n";
echo "============================\n";
echo "The system uses a 3-tier pricing structure:\n\n";

echo "🔹 TIER 1: First 1000 units (0-1000)\n";
echo "   - Rate: units_0_1 (from water_bill_rates table)\n";
echo "   - Example: If units_0_1 = 15.50, then first 1000 units cost 15.50 each\n\n";

echo "🔹 TIER 2: Next 4000 units (1001-5000)\n";
echo "   - Rate: units_1_5 (from water_bill_rates table)\n";
echo "   - Example: If units_1_5 = 25.00, then units 1001-5000 cost 25.00 each\n\n";

echo "🔹 TIER 3: Above 5000 units (5001+)\n";
echo "   - Rate: units_above_5 (from water_bill_rates table)\n";
echo "   - Example: If units_above_5 = 35.00, then units 5001+ cost 35.00 each\n\n";

echo "🔹 SERVICE CHARGE: Fixed amount\n";
echo "   - Rate: service (from water_bill_rates table)\n";
echo "   - Example: If service = 100.00, then 100.00 is added to every bill\n\n";

echo "🧮 CALCULATION ALGORITHM:\n";
echo "=========================\n";
echo "```php\n";
echo "private function calculateBillAmount(\$unitsConsumed, \$billRate)\n";
echo "{\n";
echo "    \$totalAmount = 0;\n";
echo "    \n";
echo "    // Tier 1: First 1000 units (0-1000)\n";
echo "    if (\$unitsConsumed > 0) {\n";
echo "        \$tier1Units = min(\$unitsConsumed, 1000);\n";
echo "        \$totalAmount += \$tier1Units * \$billRate->units_0_1;\n";
echo "    }\n";
echo "    \n";
echo "    // Tier 2: Next 4000 units (1001-5000)\n";
echo "    if (\$unitsConsumed > 1000) {\n";
echo "        \$tier2Units = min(\$unitsConsumed - 1000, 4000);\n";
echo "        \$totalAmount += \$tier2Units * \$billRate->units_1_5;\n";
echo "    }\n";
echo "    \n";
echo "    // Tier 3: Above 5000 units\n";
echo "    if (\$unitsConsumed > 5000) {\n";
echo "        \$tier3Units = \$unitsConsumed - 5000;\n";
echo "        \$totalAmount += \$tier3Units * \$billRate->units_above_5;\n";
echo "    }\n";
echo "    \n";
echo "    // Add service charge (fixed amount)\n";
echo "    \$totalAmount += \$billRate->service;\n";
echo "    \n";
echo "    return \$totalAmount;\n";
echo "}\n";
echo "```\n\n";

echo "📊 CALCULATION EXAMPLES:\n";
echo "========================\n\n";

echo "Example 1: 500 units consumed\n";
echo "-----------------------------\n";
echo "Tier 1: 500 units × 15.50 = 7,750.00\n";
echo "Service: + 100.00\n";
echo "Total: 7,750.00 + 100.00 = 7,850.00\n\n";

echo "Example 2: 2,000 units consumed\n";
echo "------------------------------\n";
echo "Tier 1: 1,000 units × 15.50 = 15,500.00\n";
echo "Tier 2: 1,000 units × 25.00 = 25,000.00\n";
echo "Service: + 100.00\n";
echo "Total: 15,500.00 + 25,000.00 + 100.00 = 40,600.00\n\n";

echo "Example 3: 6,000 units consumed\n";
echo "------------------------------\n";
echo "Tier 1: 1,000 units × 15.50 = 15,500.00\n";
echo "Tier 2: 4,000 units × 25.00 = 100,000.00\n";
echo "Tier 3: 1,000 units × 35.00 = 35,000.00\n";
echo "Service: + 100.00\n";
echo "Total: 15,500.00 + 100,000.00 + 35,000.00 + 100.00 = 150,600.00\n\n";

echo "🔄 INTEGRATION PROCESS:\n";
echo "=======================\n";
echo "1. User creates meter reading via POST /api/water-meter-readings\n";
echo "2. System calculates units consumed (current_reading - previous_reading)\n";
echo "3. System gets water customer's water scheme\n";
echo "4. System finds bill rates for that water scheme\n";
echo "5. System applies tiered pricing calculation\n";
echo "6. System creates water bill with calculated amount\n";
echo "7. System returns meter reading + water bill data\n\n";

echo "📋 WATER BILL CREATION:\n";
echo "=======================\n";
echo "```php\n";
echo "\$waterBill = WaterBill::create([\n";
echo "    'water_customer_id' => \$request->water_customer_id,\n";
echo "    'meter_reader_id' => \$meterReaderId,\n";
echo "    'billing_month' => \$request->reading_month,\n";
echo "    'due_date' => date('Y-m-d', strtotime(\$request->reading_month . ' +30 days')),\n";
echo "    'amount_due' => \$billAmount,\n";
echo "    'status' => 1, // 1 = unpaid\n";
echo "]);\n";
echo "```\n\n";

echo "🎯 KEY FEATURES:\n";
echo "================\n";
echo "✅ Automatic bill calculation on meter reading\n";
echo "✅ Tiered pricing structure (3 tiers)\n";
echo "✅ Service charge included\n";
echo "✅ Water scheme specific rates\n";
echo "✅ Due date = billing month + 30 days\n";
echo "✅ Status automatically set to unpaid (1)\n";
echo "✅ Integration with water_bill_rates table\n\n";

echo "💡 This logic ensures fair and progressive pricing\n";
echo "   where higher consumption is charged at higher rates.\n";
