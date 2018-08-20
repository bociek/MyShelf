<?php
/**
 * Tags repository.
 */
namespace Repository;
use Doctrine\DBAL\Connection;
/**
 * Class TagsRepository.
 */
class TagsRepository
{
    /**
     * Number of items per page.
     *
     * const int NUM_ITEMS
     */
    const NUM_ITEMS = 3;
    /**
     * Doctrine DBAL connection.
     *
     * @var \Doctrine\DBAL\Connection $db
     */
    protected $db;
    /**
     * TagsRepository constructor.
     *
     * @param \Doctrine\DBAL\Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }
    /**
     * Fetch all records.
     *
     * @return array Result
     */
    public function findAll()
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('id', 'name')
            ->from('si_tags');
        return $queryBuilder->execute()->fetchAll();
    }
    /**
     * Find one record.
     *
     * @param string $id Element id
     *
     * @return array|mixed Result
     */
    public function findOneById($id)
    {
        $queryBuilder = $this->queryAll();
        $queryBuilder->where('t.id = :id')
            ->setParameter(':id', $id, \PDO::PARAM_INT);
        $result = $queryBuilder->execute()->fetch();
        return !$result ? [] : $result;
    }
    protected function queryAll()
    {
        $queryBuilder = $this->db->createQueryBuilder();
        return $queryBuilder->select('t.id', 't.name')
            ->from('si_tags', 't');
    }
    /**
     * Find one record by name.
     *
     * @param string $name Name
     *
     * @return array|mixed Result
     */
    public function findOneByName($name)
    {
        $queryBuilder = $this->queryAll();
        $queryBuilder->where('t.name = :name')
            ->setParameter(':name', $name, \PDO::PARAM_STR);
        $result = $queryBuilder->execute()->fetch();
        return !$result ? [] : $result;
    }
    /**
     * Find tags by Ids.
     *
     * @param array $ids Tags Ids.
     *
     * @return array
     */
    public function findById($ids)
    {
        $queryBuilder = $this->queryAll();
        $queryBuilder->where('t.id IN (:ids)')
            ->setParameter(':ids', $ids, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY);
        return $queryBuilder->execute()->fetchAll();
    }
    /**
     * Get records paginated.
     *
     * @param int $page Current page number
     *
     * @return array Result
     */
    public function findAllPaginated($page = 1)
    {
        $queryBuilder = $this->queryAll();
        $queryBuilder->setFirstResult(($page - 1) * static::NUM_ITEMS)
            ->setMaxResults(static::NUM_ITEMS);
        $pagesNumber = $this->countAllPages();
        $paginator = [
            'page' => ($page < 1 || $page > $pagesNumber) ? 1 : $page,
            'max_results' => static::NUM_ITEMS,
            'pages_number' => $pagesNumber,
            'data' => $queryBuilder->execute()->fetchAll(),
        ];
        return $paginator;
    }
    protected function countAllPages()
    {
        $pagesNumber = 1;
        $queryBuilder = $this->queryAll();
        $queryBuilder->select('COUNT(DISTINCT t.id) AS total_results')
            ->setMaxResults(1);
        $result = $queryBuilder->execute()->fetch();
        if ($result) {
            $pagesNumber =  ceil($result['total_results'] / static::NUM_ITEMS);
        } else {
            $pagesNumber = 1;
        }
        return $pagesNumber;
    }
    /**
     * Save record.
     *
     * @param array $tag Tag
     *
     * @return boolean Result
     */
    public function save($tag)
    {
        if (isset($tag['id']) && ctype_digit((string) $tag['id'])) {
            // update record
            $id = $tag['id'];
            unset($tag['id']);
            return $this->db->update('si_tags', $tag, ['id' => $id]);
        } else {
            // add new record
            $this->db->insert('si_tags', $tag);
            $tag['id'] = $this->db->lastInsertId();
            return $tag;
        }
    }
}