<?php

class Protocol{
    const HEAD_LENGTH = 4 + 2 + 4;
    const HEAD_PACK_FORMAT = 'nNN';
    const HEAD_UNPACK_FORMAT = 'ntype/Nid/Nlegth';

    public static function  pack(int $type, int $id, string $data): string
    {
        return pack(static::HEAD_UNPACK_FORMAT, $type, $id, strlen($data)) . $data;
    }

    public static function unpack(string $data): array
    {
        $ret = unpack(static:: HEAD_UNPACK_FORMAT, $data);
        return $ret;
    }
}