<?php

namespace App\Database;

use Illuminate\Database\SqlServerConnection;

class LegacySqlServerConnection extends SqlServerConnection
{
    /**
     * Run a select statement against the database.
     *
     * Raw SQL across this app declares SQL Server variables and assigns them in a
     * standalone statement before the real query (`declare @x date; select @x = :x;
     * select ... where col = @x`). Under this app's PDO_DBLIB/FreeTDS connection that
     * standalone assignment statement emits its own empty result set as the first
     * rowset, and the parent select() only ever reads the first rowset - so the real
     * rows are silently dropped. Confirmed via nextRowset(): rowset #0 is empty,
     * rowset #1 holds the actual data. Walk forward to the first non-empty rowset
     * instead. A query that legitimately matches nothing still returns [] once
     * nextRowset() runs out, so ordinary (non multi-statement) queries are unaffected.
     */
    public function select($query, $bindings = [], $useReadPdo = true, array $fetchUsing = [])
    {
        return $this->run($query, $bindings, function ($query, $bindings) use ($useReadPdo, $fetchUsing) {
            if ($this->pretending()) {
                return [];
            }

            $statement = $this->prepared(
                $this->getPdoForSelect($useReadPdo)->prepare($query)
            );

            $this->bindValues($statement, $this->prepareBindings($bindings));

            $statement->execute();

            do {
                $rows = $statement->fetchAll(...$fetchUsing);

                if (! empty($rows)) {
                    return $rows;
                }
            } while ($statement->nextRowset());

            return [];
        });
    }
}
